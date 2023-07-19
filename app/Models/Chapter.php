<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "name_bn",
        "class_level_id",
        "subject_id",
        "chapter_code",
        "price",
        "is_free",
        "icon",
        "color_code",
        "sequence",
        "is_active"
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean'
    ];
}
