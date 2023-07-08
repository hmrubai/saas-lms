<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $table = 'topics';

    protected $fillable = [
        'title',
        'hint',
        'country_id',
        'package_type_id',
        'catagory_id',
        'grade_id',
        'school_id',
        'limit',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
