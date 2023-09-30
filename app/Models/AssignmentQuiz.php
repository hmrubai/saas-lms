<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'chapter_quiz_id',
        'gained_marks',
        'has_completed'
    ];

    protected $casts = [
        'has_completed' => 'boolean',
    ];
}
