<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentJoinHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_schedule_id',
        'student_id',
        'join_time',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
