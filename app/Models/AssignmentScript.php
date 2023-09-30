<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentScript extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'chapter_script_id',
        'has_completed'
    ];

    protected $casts = [
        'has_completed' => 'boolean',
    ];
}
