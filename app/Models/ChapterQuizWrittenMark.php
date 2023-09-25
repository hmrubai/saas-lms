<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuizWrittenMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_quiz_result_id',
        'chapter_quiz_id',
        'user_id',
        'question_no', 
        'mark', 
        'marks_givenby_id'
    ];

    protected $casts = [];
}
