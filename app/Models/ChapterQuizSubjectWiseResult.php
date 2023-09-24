<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuizSubjectWiseResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_quiz_result_id',
        'chapter_quiz_id',
        'user_id',
        'quiz_core_subject_id',
        'positive_count',
        'negetive_count'
    ];

    protected $casts = [];

}
