<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterQuizResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chapter_quiz_id',
        'course_id',
        'submission_status',
        'mark',
        'positive_count',
        'negetive_count'
    ];
}
