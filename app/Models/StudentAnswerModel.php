<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentAnswerModel extends Model
{
    protected $table = 'student_answers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['attempt_id', 'question_id', 'written_answer', 'marks_awarded'];
}
