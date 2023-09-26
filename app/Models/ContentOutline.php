<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentOutline extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_bn',
        'content_id',
        'class_level_id',
        'content_subject_id',
        'subject_id',
        'chapter_id',
        'chapter_script_id',
        'chapter_video_id',
        'chapter_quiz_id',
        'icon',
        'color_code',
        'is_free',
        'sequence',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean'
    ];
}
