<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuizSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_quiz_id',
        'quiz_core_subject_id',
        'no_of_question',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
