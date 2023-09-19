<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuizWrittenQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_quiz_id',
        'question_attachment',
        'marks',
        'no_of_question',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
