<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterScript extends Model
{
    use HasFactory;
    protected $table = 'chapter_scripts';

    protected $fillable = [
        'title',
        'title_bn',
        'description',
        'script_code',
        'class_level_id',
        'subject_id',
        'chapter_id',
        'raw_url',
        's3_url',
        'thumbnail',
        'price',
        'rating',
        'is_free',
        'sequence',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean'
    ];
}
