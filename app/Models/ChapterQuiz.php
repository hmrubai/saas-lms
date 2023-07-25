<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuiz extends Model
{
    use HasFactory;
    protected $table = 'chapter_quizzes';

    protected $fillable = [
        'title',
        'title_bn',
        'description',
        'quiz_code',
        'class_level_id',
        'subject_id',
        'chapter_id',
        'duration',
        'positive_mark',
        'negative_mark',
        'total_mark',
        'number_of_question',
        'is_free',
        'sequence',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean'
    ];
}
