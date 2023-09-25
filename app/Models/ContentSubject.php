<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'class_level_id',
        'subject_id',
        'price',
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
