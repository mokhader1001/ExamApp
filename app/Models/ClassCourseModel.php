<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassCourseModel extends Model
{
    protected $table = 'class_courses';
    protected $primaryKey = 'class_id'; // Note: it's a composite key, but CI model needs a string
    protected $allowedFields = ['class_id', 'course_id'];
    protected $useTimestamps = false;

    public function getCoursesByClass($classId)
    {
        return $this->db->table('class_courses')
            ->select('courses.*')
            ->join('courses', 'courses.id = class_courses.course_id')
            ->where('class_id', $classId)
            ->get()
            ->getResultArray();
    }
}
