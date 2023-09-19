<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizParticipationCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'chapter_quiz_id',
        'user_id',
        'number_of_participation',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

}
