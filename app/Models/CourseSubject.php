<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'course_id',
        'link',
        'is_optional',
        'is_active'
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'is_active' => 'boolean'
    ];
}
