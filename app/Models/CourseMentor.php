<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMentor extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'mentor_id',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',

    ];

}
