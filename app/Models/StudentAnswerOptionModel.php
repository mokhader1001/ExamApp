<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentAnswerOptionModel extends Model
{
    protected $table = 'student_answer_options';
    protected $primaryKey = 'answer_id'; // Composite key workaround
    protected $returnType = 'array';
    protected $allowedFields = ['answer_id', 'option_id'];
}
