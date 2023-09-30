<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'course_id',
        'title',
        'title_bn',
        'description',
        'publish_date',
        'deadline',
        'status',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
