<?php

namespace App\Controllers;

use App\Models\ClassModel;
use App\Models\CourseModel;

class Dashboard extends BaseController
{
    public function teacher()
    {
        $role = session()->get('role');
        if (!in_array($role, ['teacher', 'admin']))
            return redirect()->to('/login');

        $classModel = new ClassModel();
        $courseModel = new CourseModel();
        $teacherId = session()->get('user_id');

        if ($role === 'teacher') {
            $classCount = $classModel->join('user_classes', 'user_classes.class_id = classes.id')
                ->where('user_classes.user_id', $teacherId)
                ->countAllResults();

            $courseCountData = (new \App\Models\ClassCourseModel())
                ->select('COUNT(DISTINCT class_courses.course_id) AS total', false)
                ->join('user_classes', 'user_classes.class_id = class_courses.class_id')
                ->where('user_classes.user_id', $teacherId)
                ->first();
            $courseCount = $courseCountData['total'] ?? 0;
        } else {
            $classCount = $classModel->countAll();
            $courseCount = $courseModel->countAll();
        }

        $data = [
            'classCount' => $classCount,
            'courseCount' => $courseCount,
        ];

        return view('teacher/dashboard', $data);
    }

    public function student()
    {
        if (session()->get('role') !== 'student')
            return redirect()->to('/login');

        $userId = session()->get('user_id');
        $userClassModel = new \App\Models\UserClassModel();
        $classCourseModel = new \App\Models\ClassCourseModel();

        // Get student's class
        $enrollment = $userClassModel->select('classes.class_name, classes.id')
            ->join('classes', 'classes.id = user_classes.class_id')
            ->where('user_id', $userId)
            ->first();

        $data['class'] = $enrollment;
        $data['courses'] = [];

        if ($enrollment) {
            $data['courses'] = $classCourseModel->getCoursesByClass($enrollment['id']);
        }

        return view('student/dashboard', $data);
    }
}
