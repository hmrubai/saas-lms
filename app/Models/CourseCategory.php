<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
