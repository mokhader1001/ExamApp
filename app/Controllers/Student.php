<?php

namespace App\Controllers;

use App\Models\ExamModel;
use App\Models\ExamAttemptModel;
use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use App\Models\StudentAnswerModel;
use App\Models\StudentAnswerOptionModel;
use App\Models\UserClassModel;
use App\Models\ClassCourseModel;

class Student extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('student/dashboard'));
    }

    public function exams()
    {
        $userId = session()->get('user_id');
        $userClassModel = new UserClassModel();
        $classCourseModel = new ClassCourseModel();
        $examModel = new ExamModel();
        $attemptModel = new ExamAttemptModel();

        // Get student's class
        $userClass = $userClassModel->where('user_id', $userId)->first();
        if (!$userClass) {
            $data['exams'] = [];
            return view('student/exams/index', $data);
        }

        $classId = $userClass['class_id'];

        // Get courses assigned to this class
        $assignedCourses = $classCourseModel->where('class_id', $classId)->findAll();
        $courseIds = array_column($assignedCourses, 'course_id');

        if (empty($courseIds)) {
            $data['exams'] = [];
            return view('student/exams/index', $data);
        }

        // Get active exams for these courses
        $exams = $examModel->select('exams.*, courses.course_name')
            ->join('courses', 'courses.id = exams.course_id')
            ->whereIn('exams.course_id', $courseIds)
            ->where('exams.status', 'active')
            ->findAll();

        // Check attempt status for each exam
        foreach ($exams as &$exam) {
            $attempt = $attemptModel->where('exam_id', $exam['id'])
                ->where('student_id', $userId)
                ->first();
            $exam['attempt'] = $attempt;
        }

        $data['exams'] = $exams;
        return view('student/exams/index', $data);
    }

    public function enterExam($examId)
    {
        $userId = session()->get('user_id');
        $examModel = new ExamModel();
        $attemptModel = new ExamAttemptModel();
        $questionModel = new QuestionModel();
        $optionModel = new QuestionOptionModel();

        $exam = $examModel->select('exams.*, courses.course_name')
            ->join('courses', 'courses.id = exams.course_id')
            ->find($examId);
        if (!$exam || $exam['status'] !== 'active') {
            return redirect()->to(site_url('student/dashboard'))->with('error', 'Exam is not available.');
        }

        // Check if already submitted or canceled
        $existingAttempt = $attemptModel->where('exam_id', $examId)
            ->where('student_id', $userId)
            ->first();

        if ($existingAttempt && $existingAttempt['status'] !== 'in_progress') {
            return redirect()->to(site_url('student/dashboard'))->with('info', 'You have already completed this exam.');
        }

        // Start or resume attempt
        if (!$existingAttempt) {
            $attemptId = $attemptModel->insert([
                'student_id' => $userId,
                'exam_id' => $examId,
                'status' => 'in_progress',
                'start_time' => date('Y-m-d H:i:s')
            ]);
            $attempt = $attemptModel->find($attemptId);
        } else {
            $attempt = $existingAttempt;
        }

        // Fetch questions, options, and existing answers
        $questions = $questionModel->where('exam_id', $examId)->findAll();
        $answerModel = new StudentAnswerModel();
        $ansOptModel = new StudentAnswerOptionModel();

        foreach ($questions as &$q) {
            $q['options'] = $optionModel->where('question_id', $q['id'])->findAll();

            // Check for saved answer
            $savedAns = $answerModel->where('attempt_id', $attempt['id'])
                ->where('question_id', $q['id'])
                ->first();

            if ($savedAns) {
                $q['saved_answer'] = $savedAns;
                // If it might be multi-option (checkbox/radio), fetch selected options
                $selectedOpts = $ansOptModel->where('answer_id', $savedAns['id'])->findAll();
                $q['saved_options'] = array_column($selectedOpts, 'option_id');
            } else {
                $q['saved_answer'] = null;
                $q['saved_options'] = [];
            }
        }

        // Calculate remaining time
        $startTime = strtotime($attempt['start_time']);
        $durationSeconds = $exam['duration_minutes'] * 60;
        $endTime = $startTime + $durationSeconds;
        $remainingSeconds = max(0, $endTime - time());

        helper('setting');
        $data['exam'] = $exam;
        $data['attempt'] = $attempt;
        $data['questions'] = $questions;
        $data['remaining_seconds'] = $remainingSeconds;

        $data['switch_warning_msg'] = get_setting('tab_switch_warning', 'WARNING: Tab switching detected!');
        $data['switch_kick_msg'] = get_setting('tab_switch_kick', 'EXAM CANCELED: You have exceeded the tab switch limit.');

        return view('student/exams/take', $data);
    }

    public function recordTabSwitch($attemptId)
    {
        $attemptModel = new ExamAttemptModel();
        $attempt = $attemptModel->find($attemptId);

        if ($attempt && $attempt['status'] === 'in_progress') {
            $newCount = $attempt['tab_switch_count'] + 1;
            $attemptModel->update($attemptId, ['tab_switch_count' => $newCount]);

            return $this->response->setJSON(['status' => 'success', 'count' => $newCount]);
        }

        return $this->response->setJSON(['status' => 'error']);
    }

    public function saveProgress()
    {
        $attemptId = $this->request->getPost('attempt_id');
        $questionId = $this->request->getPost('question_id');
        $answer = $this->request->getPost('answer'); // One value (string, id) or array (checkboxes)

        $attemptModel = new ExamAttemptModel();
        $answerModel = new StudentAnswerModel();
        $ansOptModel = new StudentAnswerOptionModel();
        $userId = session()->get('user_id');

        $attempt = $attemptModel->find($attemptId);
        if (!$attempt || $attempt['student_id'] != $userId || $attempt['status'] !== 'in_progress') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid attempt']);
        }

        // Check if we already have an answer record for this question
        $existing = $answerModel->where('attempt_id', $attemptId)->where('question_id', $questionId)->first();

        if ($existing) {
            $answerId = $existing['id'];
            // Update written answer if provided (or null it if now switching to options? likely just update)
            $answerModel->update($answerId, [
                'written_answer' => is_string($answer) && !is_numeric($answer) ? $answer : null
            ]);
            // Clear old options
            $ansOptModel->where('answer_id', $answerId)->delete();
        } else {
            $answerId = $answerModel->insert([
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
                'written_answer' => is_string($answer) && !is_numeric($answer) ? $answer : null
            ]);
        }

        // Insert new options
        if (is_array($answer)) {
            foreach ($answer as $optId) {
                $ansOptModel->insert(['answer_id' => $answerId, 'option_id' => $optId]);
            }
        } elseif (is_numeric($answer)) {
            $ansOptModel->insert(['answer_id' => $answerId, 'option_id' => $answer]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function submitExam()
    {
        $attemptId = $this->request->getPost('attempt_id');
        $attemptModel = new ExamAttemptModel();
        $answerModel = new StudentAnswerModel();
        $ansOptModel = new StudentAnswerOptionModel();

        $attempt = $attemptModel->find($attemptId);
        if (!$attempt || $attempt['status'] !== 'in_progress') {
            return redirect()->to(site_url('student/dashboard'));
        }

        $answersRaw = $this->request->getPost('answers');

        if ($answersRaw) {
            foreach ($answersRaw as $qId => $ans) {
                $qData = [
                    'attempt_id' => $attemptId,
                    'question_id' => $qId,
                    'written_answer' => is_string($ans) ? $ans : null
                ];
                $ansId = $answerModel->insert($qData);

                if (is_array($ans)) { // Checkbox or multi
                    foreach ($ans as $optId) {
                        $ansOptModel->insert(['answer_id' => $ansId, 'option_id' => $optId]);
                    }
                } elseif (is_numeric($ans)) { // Radio/Select
                    $ansOptModel->insert(['answer_id' => $ansId, 'option_id' => $ans]);
                }
            }
        }

        $attemptModel->update($attemptId, [
            'status' => 'submitted',
            'submit_time' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(site_url('student/dashboard'))->with('success', 'Exam submitted successfully.');
    }

    public function forceSubmit()
    {
        $attemptId = $this->request->getPost('attempt_id');
        $attemptModel = new ExamAttemptModel();
        $answerModel = new StudentAnswerModel();
        $ansOptModel = new StudentAnswerOptionModel();

        $userId = session()->get('user_id');
        $attempt = $attemptModel->find($attemptId);

        if (!$attempt || $attempt['student_id'] != $userId || $attempt['status'] !== 'in_progress') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid attempt']);
        }

        $answersRaw = $this->request->getPost('answers');

        if ($answersRaw) {
            foreach ($answersRaw as $qId => $ans) {
                $qData = [
                    'attempt_id' => $attemptId,
                    'question_id' => $qId,
                    'written_answer' => is_string($ans) ? $ans : null
                ];
                $ansId = $answerModel->insert($qData);

                if (is_array($ans)) {
                    foreach ($ans as $optId) {
                        $ansOptModel->insert(['answer_id' => $ansId, 'option_id' => $optId]);
                    }
                } elseif (is_numeric($ans)) {
                    $ansOptModel->insert(['answer_id' => $ansId, 'option_id' => $ans]);
                }
            }
        }

        $attemptModel->update($attemptId, [
            'status' => 'submitted',
            'submit_time' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function viewResult($attemptId)
    {
        $userId = session()->get('user_id');
        $attemptModel = new ExamAttemptModel();
        $examModel = new ExamModel();
        $questionModel = new QuestionModel();
        $answerModel = new StudentAnswerModel();
        $optModel = new QuestionOptionModel();
        $ansOptModel = new StudentAnswerOptionModel();

        $attempt = $attemptModel->find($attemptId);
        if (!$attempt || $attempt['student_id'] != $userId || $attempt['status'] !== 'submitted' || $attempt['is_released'] == 0) {
            return redirect()->to(site_url('student/dashboard'))->with('error', 'Result not available or not yet released by instructor.');
        }

        $exam = $examModel->select('exams.*, courses.course_name')
            ->join('courses', 'courses.id = exams.course_id')
            ->find($attempt['exam_id']);

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

        // Calculate total points
        $totalPoints = (new QuestionModel())->where('exam_id', $exam['id'])->selectSum('points')->get()->getRow()->points ?? 0;
        $data['total_possible_marks'] = $totalPoints;

        return view('student/exams/result', $data);
    }
}
