<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $table = 'subjects';

    protected $fillable = [
        "name",
        "name_bn",
        "subject_code",
        "class_level_id",
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
