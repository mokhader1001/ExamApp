<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamAttemptModel extends Model
{
    protected $table = 'exam_attempts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_id',
        'exam_id',
        'start_time',
        'submit_time',
        'tab_switch_count',
        'status',
        'calculated_score',
        'final_score',
        'markup_deducted',
        'teacher_comment',
        'is_released'
    ];

    protected $useTimestamps = false;
}
