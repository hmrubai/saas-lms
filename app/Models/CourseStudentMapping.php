<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseStudentMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'mentor_id',
        'student_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
