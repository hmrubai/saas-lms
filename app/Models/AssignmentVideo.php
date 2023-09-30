<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'chapter_video_id',
        'has_completed'
    ];

    protected $casts = [
        'has_completed' => 'boolean',
    ];
}
