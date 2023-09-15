<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuizResultAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_quiz_result_id',
        'question_id',
        'answer1',
        'answer2',
        'answer3',
        'answer4',
        'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean'
    ];
}
