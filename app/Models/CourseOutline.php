<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOutline extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'title_bn',
        'course_id',
        'class_level_id',
        'subject_id',
        'chapter_id',
        'chapter_script_id',
        'chapter_video_id',
        'chapter_quiz_id',
        'is_free',
        'sequence',
        'is_active',
        'is_only_note'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'is_only_note' => 'boolean'
    ];

}
