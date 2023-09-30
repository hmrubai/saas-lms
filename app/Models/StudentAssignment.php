<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'given_video',
        'completed_video',
        'given_quiz',
        'completed_quiz',
        'given_script',
        'completed_script',
        'total_progress',
        'total_progress_parcentage'
    ];

    protected $casts = [];
}
