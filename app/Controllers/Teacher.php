<?php

namespace App\Controllers;

use App\Models\ClassModel;
use App\Models\CourseModel;
use App\Models\UserClassModel;
use App\Models\ExamModel;
use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use App\Models\ExamAttemptModel;
use App\Models\StudentAnswerModel;
use App\Models\StudentAnswerOptionModel;

class Teacher extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $role = session()->get('role');
        if (!in_array($role, ['teacher', 'admin'])) {
            header('Location: ' . site_url('login'));
            exit;
        }
    }

    // --- Classes ---
    public function classes()
    {
        $classModel = new ClassModel();
        $courseModel = new CourseModel();
        $teacherId = session()->get('user_id');
        $role = session()->get('role');

        $query = $classModel->select('classes.*, COUNT(class_courses.course_id) as course_count')
            ->join('class_courses', 'class_courses.class_id = classes.id', 'left');

        // Scope to assigned classes if role is teacher
        if ($role === 'teacher') {
            $query->join('user_classes', 'user_classes.class_id = classes.id')
                ->where('user_classes.user_id', $teacherId);
        }

        $data['classes'] = $query->groupBy('classes.id')->findAll();
        $data['all_courses'] = $courseModel->findAll();
        return view('teacher/classes/index', $data);
    }

    public function getAssignedCourses($classId)
    {
        $teacherId = session()->get('user_id');
        $role = session()->get('role');
        $userClassModel = new \App\Models\UserClassModel();

        // Security check for teachers
        if ($role === 'teacher') {
            $isAssigned = $userClassModel->where(['user_id' => $teacherId, 'class_id' => $classId])->first();
            if (!$isAssigned)
                return $this->response->setJSON([]);
        }

        $classCourseModel = new \App\Models\ClassCourseModel();
        $assigned = $classCourseModel->where('class_id', $classId)->findAll();
        return $this->response->setJSON(array_column($assigned, 'course_id'));
    }

    public function saveClass()
    {
        $classModel = new ClassModel();
        $id = $this->request->getPost('id');

        $rules = [
            'class_name' => $id ? "required|is_unique[classes.class_name,id,{$id}]" : "required|is_unique[classes.class_name]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Class name already exists or invalid.');
        }

        $data = [
            'class_name' => $this->request->getPost('class_name')
        ];

        if ($id) {
            $classModel->update($id, $data);
            $msg = 'Class updated successfully.';
        } else {
            $id = $classModel->insert($data);
            $msg = 'Class added successfully.';

            // Auto-assign the creator (teacher) to the class
            if (session()->get('role') === 'teacher') {
                $userClassModel = new \App\Models\UserClassModel();
                $userClassModel->insert([
                    'user_id' => session()->get('user_id'),
                    'class_id' => $id
                ]);
            }
        }

        return redirect()->to(site_url('teacher/classes'))->with('success', $msg);
    }

    public function deleteClass($id)
    {
        $userClassModel = new UserClassModel();
        $enrollmentCount = $userClassModel->where('class_id', $id)->countAllResults();

        if ($enrollmentCount > 0) {
            return redirect()->to(site_url('teacher/classes'))->with('error', 'Cannot delete class. It has registered students.');
        }

        $classModel = new ClassModel();
        $classModel->delete($id);
        return redirect()->to(site_url('teacher/classes'))->with('success', 'Class deleted successfully.');
    }

    // --- Courses ---
    public function courses()
    {
        $courseModel = new CourseModel();
        $data['courses'] = $courseModel->findAll();
        return view('teacher/courses/index', $data);
    }

    // --- Exams ---
    public function exams()
    {
        $examModel = new ExamModel();
        $courseModel = new CourseModel();
        $teacherId = session()->get('user_id');
        $role = session()->get('role');

        $query = $examModel->select('exams.*, courses.course_name')
            ->join('courses', 'courses.id = exams.course_id');

        if ($role === 'teacher') {
            $query->where('exams.teacher_id', $teacherId);
        }

        $data['exams'] = $query->findAll();
        $data['courses'] = $courseModel->findAll();
        return view('teacher/exams/index', $data);
    }

    public function createExam()
    {
        $courseModel = new CourseModel();
        $data['courses'] = $courseModel->findAll();
        $data['exam'] = null; // New exam
        return view('teacher/exams/form', $data);
    }

    public function editExam($id)
    {
        $examModel = new ExamModel();
        $courseModel = new CourseModel();
        $questionModel = new QuestionModel();
        $optionModel = new QuestionOptionModel();

        $exam = $examModel->find($id);
        if (!$exam)
            return redirect()->to(site_url('teacher/exams'))->with('error', 'Exam not found.');

        // Fetch questions and options
        $questions = $questionModel->where('exam_id', $id)->findAll();
        foreach ($questions as &$q) {
            $q['options'] = $optionModel->where('question_id', $q['id'])->findAll();
        }

        $data['exam'] = $exam;
        $data['questions'] = $questions;
        $data['courses'] = $courseModel->findAll();

        return view('teacher/exams/form', $data);
    }

    public function saveExamFull()
    {
        $db = \Config\Database::connect();
        $examModel = new ExamModel();
        $questionModel = new QuestionModel();
        $optionModel = new QuestionOptionModel();

        $examId = $this->request->getPost('exam_id');
        $teacherId = session()->get('user_id');

        $examData = [
            'title' => $this->request->getPost('title'),
            'course_id' => $this->request->getPost('course_id'),
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'tab_switch_limit' => $this->request->getPost('tab_switch_limit'),
            'tab_switch_action' => $this->request->getPost('tab_switch_action'),
            'status' => $this->request->getPost('status') ?? 'draft',
            'teacher_id' => $teacherId
        ];

        $db->transStart();

        if ($examId) {
            $examModel->update($examId, $examData);
        } else {
            $examId = $examModel->insert($examData);
        }

        // Process Questions
        // Logic: Delete old questions and options to perform a clean overwrite (simpler for dynamic forms)
        $questionModel->where('exam_id', $examId)->delete(); // This triggers cascading logic if DB supports it, else we handle manually

        $questionsRaw = $this->request->getPost('questions');
        if ($questionsRaw) {
            foreach ($questionsRaw as $qIndex => $q) {
                $qData = [
                    'exam_id' => $examId,
                    'question_text' => $q['text'],
                    'question_type' => $q['type'],
                    'points' => $q['points']
                ];
                $newQId = $questionModel->insert($qData);

                if (in_array($q['type'], ['mcq', 'checkbox', 'dropdown']) && isset($q['options'])) {
                    foreach ($q['options'] as $optIndex => $optText) {
                        if (empty($optText))
                            continue;

                        $isCorrect = false;
                        if ($q['type'] === 'checkbox') {
                            $isCorrect = isset($q['correct']) && in_array($optIndex, $q['correct']);
                        } else {
                            $isCorrect = (isset($q['correct']) && $q['correct'] == $optIndex);
                        }

                        $optionModel->insert([
                            'question_id' => $newQId,
                            'option_text' => $optText,
                            'is_correct' => $isCorrect ? 1 : 0
                        ]);
                    }
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->withInput()->with('error', 'Failed to save exam details.');
        }

        return redirect()->to(site_url('teacher/exams'))->with('success', 'Exam saved successfully.');
    }

    public function saveExam()
    {
        $examModel = new ExamModel();
        $id = $this->request->getPost('id');
        $teacherId = session()->get('user_id');

        $data = [
            'title' => $this->request->getPost('title'),
            'course_id' => $this->request->getPost('course_id'),
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'tab_switch_limit' => $this->request->getPost('tab_switch_limit'),
            'tab_switch_action' => $this->request->getPost('tab_switch_action'),
            'status' => $id ? $this->request->getPost('status') : 'draft',
            'teacher_id' => $teacherId
        ];

        if ($id) {
            $examModel->update($id, $data);
            $msg = 'Exam updated successfully.';
        } else {
            $examModel->insert($data);
            $msg = 'Exam created successfully.';
        }

        return redirect()->to(site_url('teacher/exams'))->with('success', $msg);
    }

    public function updateExamStatus($id, $status)
    {
        $examModel = new ExamModel();
        $examModel->update($id, ['status' => $status]);
        return redirect()->to(site_url('teacher/exams'))->with('success', 'Exam status updated to ' . $status);
    }

    public function deleteExam($id)
    {
        $examModel = new ExamModel();
        $examModel->delete($id);
        return redirect()->to(site_url('teacher/exams'))->with('success', 'Exam deleted successfully.');
    }

    // --- Questions ---
    public function manageQuestions($examId)
    {
        $examModel = new ExamModel();
        $questionModel = new QuestionModel();
        $courseModel = new CourseModel();

        $exam = $examModel->select('exams.*, courses.course_name')
            ->join('courses', 'courses.id = exams.course_id')
            ->find($examId);

        if (!$exam)
            return redirect()->to(site_url('teacher/exams'))->with('error', 'Exam not found.');

        $data['exam'] = $exam;
        $data['questions'] = $questionModel->where('exam_id', $examId)->findAll();

        // Fetch options for all questions
        $optionModel = new QuestionOptionModel();
        foreach ($data['questions'] as &$q) {
            $q['options'] = $optionModel->where('question_id', $q['id'])->findAll();
        }

        return view('teacher/questions/index', $data);
    }

    public function saveQuestion()
    {
        $questionModel = new QuestionModel();
        $optionModel = new QuestionOptionModel();

        $id = $this->request->getPost('id');
        $examId = $this->request->getPost('exam_id');

        $questionData = [
            'exam_id' => $examId,
            'question_text' => $this->request->getPost('question_text'),
            'question_type' => $this->request->getPost('question_type'),
            'points' => $this->request->getPost('points'),
        ];

        if ($id) {
            $questionModel->update($id, $questionData);
            $questionId = $id;
        } else {
            $questionId = $questionModel->insert($questionData);
        }

        // Handle Options (for MCQ, Checkbox, Dropdown)
        if (in_array($questionData['question_type'], ['mcq', 'checkbox', 'dropdown'])) {
            // Clear existing options if updating
            if ($id) {
                $optionModel->where('question_id', $id)->delete();
            }

            $optionTexts = $this->request->getPost('option_text');
            $correctIndices = $this->request->getPost('is_correct') ?? [];

            if ($optionTexts) {
                foreach ($optionTexts as $index => $text) {
                    if (empty($text))
                        continue;

                    $isCorrect = false;
                    if ($questionData['question_type'] === 'checkbox') {
                        $isCorrect = in_array($index, $correctIndices);
                    } else {
                        // For MCQ and Dropdown, is_correct is usually the index of the selected radio
                        $isCorrect = ($correctIndices == $index);
                    }

                    $optionModel->insert([
                        'question_id' => $questionId,
                        'option_text' => $text,
                        'is_correct' => $isCorrect ? 1 : 0
                    ]);
                }
            }
        }

        return redirect()->to(site_url('teacher/exams/questions/' . $examId))->with('success', 'Question saved successfully.');
    }

    public function deleteQuestion($id)
    {
        $questionModel = new QuestionModel();
        $optionModel = new QuestionOptionModel();

        $q = $questionModel->find($id);
        if (!$q)
            return redirect()->back()->with('error', 'Question not found.');

        $optionModel->where('question_id', $id)->delete();
        $questionModel->delete($id);

        return redirect()->to(site_url('teacher/exams/questions/' . $q['exam_id']))->with('success', 'Question deleted.');
    }

    public function saveCourse()
    {
        $courseModel = new CourseModel();
        $id = $this->request->getPost('id');

        $rules = [
            'course_name' => $id ? "required|is_unique[courses.course_name,id,{$id}]" : "required|is_unique[courses.course_name]",
            'fee' => 'required|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Course name already exists or invalid data.');
        }

        $data = [
            'course_name' => $this->request->getPost('course_name'),
            'description' => $this->request->getPost('description'),
            'fee' => $this->request->getPost('fee')
        ];

        if ($id) {
            $courseModel->update($id, $data);
            $msg = 'Course updated successfully.';
        } else {
            $courseModel->save($data);
            $msg = 'Course added successfully.';
        }

        return redirect()->to(site_url('teacher/courses'))->with('success', $msg);
    }

    public function deleteCourse($id)
    {
        $examModel = new ExamModel();
        $examCount = $examModel->where('course_id', $id)->countAllResults();

        if ($examCount > 0) {
            return redirect()->to(site_url('teacher/courses'))->with('error', 'Cannot delete course. It is linked to existing exams.');
        }

        $courseModel = new CourseModel();
        $courseModel->delete($id);
        return redirect()->to(site_url('teacher/courses'))->with('success', 'Course deleted successfully.');
    }

    // --- Students Management ---
    public function students()
    {
        $userModel = new \App\Models\UserModel();
        $classModel = new \App\Models\ClassModel();
        $teacherId = session()->get('user_id');
        $role = session()->get('role');

        // Fetch students in classes assigned to this teacher
        $query = $userModel->select('users.*, classes.class_name, classes.id as class_id')
            ->join('user_classes', 'user_classes.user_id = users.id', 'left')
            ->join('classes', 'classes.id = user_classes.class_id', 'left')
            ->where('users.role', 'student');

        if ($role === 'teacher') {
            // Find class IDs belonging to this teacher
            $teacherClasses = (new UserClassModel())->where('user_id', $teacherId)->findAll();
            $classIds = array_column($teacherClasses, 'class_id');

            if (!empty($classIds)) {
                $query->whereIn('classes.id', $classIds);
                $data['classes'] = $classModel->whereIn('id', $classIds)->findAll();
            } else {
                $query->where('classes.id', 0); // Force empty result if no classes assigned
                $data['classes'] = [];
            }
        } else {
            $data['classes'] = $classModel->findAll();
        }

        $data['students'] = $query->findAll();
        return view('teacher/students/index', $data);
    }

    public function saveStudent()
    {
        $userModel = new \App\Models\UserModel();
        $userClassModel = new \App\Models\UserClassModel();

        $id = $this->request->getPost('id');
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'username' => $this->request->getPost('username'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'role' => 'student'
        ];

        // Handle Password for new students
        if (!$id) {
            $data['password'] = '123456'; // UserModel will hash it
        }

        // Handle Photo Upload
        $img = $this->request->getFile('photo');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();
            if (!is_dir(FCPATH . 'uploads/students')) {
                mkdir(FCPATH . 'uploads/students', 0777, true);
            }
            $img->move(FCPATH . 'uploads/students', $newName);
            $data['photo'] = $newName;
        }

        if ($id) {
            $userModel->update($id, $data);
            $studentId = $id;
        } else {
            // Check for duplicate username
            if ($userModel->where('username', $data['username'])->first()) {
                return redirect()->back()->withInput()->with('error', 'Username already exists.');
            }
            $studentId = $userModel->insert($data);
        }

        // Assign Class
        $classId = $this->request->getPost('class_id');
        if ($classId) {
            $userClassModel->where('user_id', $studentId)->delete(); // Reset first
            $userClassModel->insert(['user_id' => $studentId, 'class_id' => $classId]);
        }

        return redirect()->to(site_url('teacher/students'))->with('success', 'Student saved successfully.');
    }

    public function deleteStudent($id)
    {
        $userModel = new \App\Models\UserModel();
        $userClassModel = new \App\Models\UserClassModel();

        $userClassModel->where('user_id', $id)->delete();
        $userModel->delete($id);

        return redirect()->to(site_url('teacher/students'))->with('success', 'Student deleted successfully.');
    }

    // --- Class-Course Assignments ---
    public function assignCourses($classId)
    {
        $classModel = new \App\Models\ClassModel();
        $courseModel = new \App\Models\CourseModel();
        $classCourseModel = new \App\Models\ClassCourseModel();

        $data['class'] = $classModel->find($classId);
        $data['all_courses'] = $courseModel->findAll();

        // Get currently assigned course IDs
        $assigned = $classCourseModel->where('class_id', $classId)->findAll();
        $data['assigned_course_ids'] = array_column($assigned, 'course_id');

        return view('teacher/classes/courses', $data);
    }

    public function saveClassCourses()
    {
        $teacherId = session()->get('user_id');
        $role = session()->get('role');
        $classId = $this->request->getPost('class_id');
        $userClassModel = new \App\Models\UserClassModel();

        // Security check for teachers
        if ($role === 'teacher') {
            $isAssigned = $userClassModel->where(['user_id' => $teacherId, 'class_id' => $classId])->first();
            if (!$isAssigned)
                return redirect()->to(site_url('teacher/classes'))->with('error', 'Unauthorized access to this class.');
        }

        $classCourseModel = new \App\Models\ClassCourseModel();
        $courseIds = $this->request->getPost('courses') ?? [];

        // Reset and re-assign
        $classCourseModel->where('class_id', $classId)->delete();

        foreach ($courseIds as $courseId) {
            $classCourseModel->insert([
                'class_id' => $classId,
                'course_id' => $courseId
            ]);
        }

        return redirect()->to(site_url('teacher/classes'))->with('success', 'Course assignments updated successfully.');
    }

    public function submissions($examId)
    {
        $examModel = new ExamModel();
        $attemptModel = new ExamAttemptModel();

        $exam = $examModel->find($examId);
        if (!$exam)
            return redirect()->to(site_url('teacher/exams'))->with('error', 'Exam not found.');

        // Get total possible marks for the exam
        $totalMarks = (new QuestionModel())->where('exam_id', $examId)->selectSum('points')->get()->getRow()->points ?? 0;

        $submissions = $attemptModel->select('exam_attempts.*, users.full_name as student_name')
            ->join('users', 'users.id = exam_attempts.student_id')
            ->where('exam_id', $examId)
            ->findAll();

        $data['exam'] = $exam;
        $data['submissions'] = $submissions;
        $data['total_possible_marks'] = $totalMarks;
        return view('teacher/exams/submissions', $data);
    }

    public function marking($attemptId)
    {
        $attemptModel = new ExamAttemptModel();
        $examModel = new ExamModel();
        $questionModel = new QuestionModel();
        $answerModel = new StudentAnswerModel();
        $optModel = new QuestionOptionModel();
        $ansOptModel = new StudentAnswerOptionModel();

        $attempt = $attemptModel->select('exam_attempts.*, users.full_name as student_name')
            ->join('users', 'users.id = exam_attempts.student_id')
            ->find($attemptId);

        if (!$attempt)
            return redirect()->to(site_url('teacher/exams'))->with('error', 'Submission not found.');

        $exam = $examModel->find($attempt['exam_id']);
        $questions = $questionModel->where('exam_id', $exam['id'])->findAll();

        foreach ($questions as &$q) {
            $q['options'] = $optModel->where('question_id', $q['id'])->findAll();
            $answer = $answerModel->where('attempt_id', $attemptId)->where('question_id', $q['id'])->first();

            if ($answer) {
                $q['student_answer'] = $answer;
                $q['selected_options'] = array_column(
                    $ansOptModel->where('answer_id', $answer['id'])->findAll(),
                    'option_id'
                );
            } else {
                $q['student_answer'] = null;
                $q['selected_options'] = [];
            }
        }

        $data['attempt'] = $attempt;
        $data['exam'] = $exam;
        $data['questions'] = $questions;
        return view('teacher/exams/marking', $data);
    }

    public function saveMarks()
    {
        $attemptId = $this->request->getPost('attempt_id');
        $marks = $this->request->getPost('marks'); // [q_id => score]
        $finalScore = $this->request->getPost('final_score');

        $attemptModel = new ExamAttemptModel();
        $answerModel = new StudentAnswerModel();

        if ($marks) {
            foreach ($marks as $qId => $score) {
                $answer = $answerModel->where('attempt_id', $attemptId)->where('question_id', $qId)->first();
                if ($answer) {
                    $answerModel->update($answer['id'], ['marks_awarded' => $score]);
                }
            }
        }

        $attemptModel->update($attemptId, [
            'final_score' => $finalScore,
            'teacher_comment' => $this->request->getPost('teacher_comment'),
            'is_released' => $this->request->getPost('is_released') ? 1 : 0,
            'status' => 'submitted' // Ensure it's marked as submitted
        ]);

        $attempt = $attemptModel->find($attemptId);
        return redirect()->to(site_url('teacher/exams/submissions/' . $attempt['exam_id']))
            ->with('success', 'Marks saved successfully.');
    }

    public function deleteSubmission($attemptId)
    {
        $attemptModel = new ExamAttemptModel();
        $answerModel = new StudentAnswerModel();
        $ansOptModel = new StudentAnswerOptionModel();

        $attempt = $attemptModel->find($attemptId);
        if (!$attempt)
            return redirect()->to(site_url('teacher/exams'))->with('error', 'Submission not found.');

        $examId = $attempt['exam_id'];

        // Delete answer options first
        $answers = $answerModel->where('attempt_id', $attemptId)->findAll();
        foreach ($answers as $ans) {
            $ansOptModel->where('answer_id', $ans['id'])->delete();
        }

        $answerModel->where('attempt_id', $attemptId)->delete();
        $attemptModel->delete($attemptId);

        return redirect()->to(site_url('teacher/exams/submissions/' . $examId))
            ->with('success', 'Submission deleted. The student can now retake the exam.');
    }

    public function releaseResult($attemptId)
    {
        $attemptModel = new ExamAttemptModel();
        $attemptModel->update($attemptId, ['is_released' => 1]);
        $attempt = $attemptModel->find($attemptId);
        return redirect()->to(site_url('teacher/exams/submissions/' . $attempt['exam_id']))
            ->with('success', 'Result released to student.');
    }
}
