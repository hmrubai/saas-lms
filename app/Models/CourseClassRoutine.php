<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClassRoutine extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'class_title',
        'is_note',
        'course_id'
    ];

    protected $casts = [
        'is_note' => 'boolean'
    ];

    
}
