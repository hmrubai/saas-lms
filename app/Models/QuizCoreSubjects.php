<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizCoreSubjects extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'is_optional',
        'optional_subject_id',
        'is_active'
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'is_active' => 'boolean'
    ];
}
