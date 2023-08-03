<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'link',
        'sequence',
        'is_authentication_needed',
        'has_submenu',
        'is_course',
        'is_content',
        'icon',
        'sequence',
        'is_active'
    ];

    protected $casts = [
        'is_authentication_needed' => 'boolean',
        'has_submenu' => 'boolean',
        'is_course' => 'boolean',
        'is_content' => 'boolean',
        'is_active' => 'boolean',
    ];
}
