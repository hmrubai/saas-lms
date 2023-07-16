<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;
    
    protected $table = 'interests';

    protected $fillable = [
        'tags'
    ];

    // protected $casts = [
    //     'is_active' => 'boolean',
    // ];
}
