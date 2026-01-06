<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->get('/register', 'Auth::register');
$routes->post('/auth/store', 'Auth::store');
$routes->post('/auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/teacher/dashboard', 'Dashboard::teacher');

// Teacher Management
$routes->group('teacher', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('classes', 'Teacher::classes');
    $routes->get('classes/add', 'Teacher::addClass');
    $routes->post('classes/save', 'Teacher::saveClass');
    $routes->get('classes/delete/(:num)', 'Teacher::deleteClass/$1');
    $routes->get('classes/courses/(:num)', 'Teacher::assignCourses/$1');
    $routes->get('classes/getAssignedCourses/(:num)', 'Teacher::getAssignedCourses/$1');
    $routes->post('classes/saveCourses', 'Teacher::saveClassCourses');

    $routes->get('courses', 'Teacher::courses');
    $routes->get('courses/add', 'Teacher::addCourse');
    $routes->post('courses/save', 'Teacher::saveCourse');
    $routes->get('courses/delete/(:num)', 'Teacher::deleteCourse/$1');

    $routes->get('students', 'Teacher::students');
    $routes->post('students/save', 'Teacher::saveStudent');
    $routes->get('students/delete/(:num)', 'Teacher::deleteStudent/$1');

    $routes->get('exams', 'Teacher::exams');
    $routes->get('exams/create', 'Teacher::createExam');
    $routes->get('exams/edit/(:num)', 'Teacher::editExam/$1');
    $routes->post('exams/saveFull', 'Teacher::saveExamFull');
    $routes->post('exams/save', 'Teacher::saveExam'); // Keep for basic updates if needed
    $routes->get('exams/status/(:num)/(:any)', 'Teacher::updateExamStatus/$1/$2');
    $routes->get('exams/delete/(:num)', 'Teacher::deleteExam/$1');
    $routes->get('exams/questions/(:num)', 'Teacher::manageQuestions/$1');
    $routes->post('exams/questions/save', 'Teacher::saveQuestion');
    $routes->get('exams/questions/delete/(:num)', 'Teacher::deleteQuestion/$1');

    // Grading Routes
    $routes->get('exams/submissions/(:num)', 'Teacher::submissions/$1');
    $routes->get('exams/marking/(:num)', 'Teacher::marking/$1');
    $routes->post('exams/saveMarks', 'Teacher::saveMarks');
    $routes->get('exams/deleteSubmission/(:num)', 'Teacher::deleteSubmission/$1');
    $routes->get('exams/releaseResult/(:num)', 'Teacher::releaseResult/$1'); // New
    $routes->get('chat/contacts', 'Chat::getContacts');
    $routes->get('chat/history/(:num)', 'Chat::getHistory/$1');
    $routes->post('chat/save', 'Chat::saveMessage');
});

// Maintenance
$routes->get('/maintenance', function () {
    helper('setting');
    return view('maintenance');
});

// Profile & Global Settings
$routes->get('/profile', 'Profile::index');
$routes->post('/profile/updateInfo', 'Profile::updateInfo');
$routes->post('/profile/updateSecurity', 'Profile::updateSecurity');
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/save', 'Settings::save');

// Student Routes with Maintenance Filter
$routes->group('student', ['filter' => 'maintenance'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::student');
    $routes->get('exams', 'Student::exams');
    $routes->get('exams/enter/(:num)', 'Student::enterExam/$1');
    $routes->post('exams/recordTabSwitch/(:num)', 'Student::recordTabSwitch/$1');
    $routes->post('exams/recordTabSwitch/(:num)', 'Student::recordTabSwitch/$1');
    $routes->post('exams/saveProgress', 'Student::saveProgress');
    $routes->post('exams/forceSubmit', 'Student::forceSubmit'); // Ensure this exists too as it was used in JS
    $routes->post('exams/submit', 'Student::submitExam');
    $routes->get('chat/contacts', 'Chat::getContacts');
    $routes->get('chat/history/(:num)', 'Chat::getHistory/$1');
    $routes->post('chat/save', 'Chat::saveMessage');
    $routes->get('exams/result/(:num)', 'Student::viewResult/$1'); // New
});
