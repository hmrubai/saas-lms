<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        "course_student_mapping_id",
        "course_id",
        "student_id",
        "mentor_id",
        "schedule_datetime",
        "has_started",
        "has_completed",
        "start_time",
        "end_time",
        "student_end_time",
        "is_active"
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_started' => 'boolean',
        'has_completed' => 'boolean'
    ];
}
