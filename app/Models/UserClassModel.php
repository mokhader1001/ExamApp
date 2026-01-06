<?php

namespace App\Models;

use CodeIgniter\Model;

class UserClassModel extends Model
{
    protected $table = 'user_classes';
    protected $primaryKey = 'user_id'; // Composite PK, but CI needs one
    protected $allowedFields = ['user_id', 'class_id'];
    protected $useTimestamps = false;
}
