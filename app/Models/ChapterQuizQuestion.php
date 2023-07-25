<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuizQuestion extends Model
{
    use HasFactory;
    protected $table = 'chapter_quiz_questions';

    protected $fillable = [
        'chapter_quiz_id',
        'class_level_id',
        'subject_id',
        'chapter_id',
        'question_text',
        'question_text_bn',
        'question_image',
        'option1',
        'option2',
        'option3',
        'option4',
        'option1_image',
        'option2_image',
        'option3_image',
        'option4_image',
        'answer1',
        'answer2',
        'answer3',
        'answer4',
        'explanation_text',
        'explanation_image',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean'
    ];
}
