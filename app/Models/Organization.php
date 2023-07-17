<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    
    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'slug',
        'details',
        'address',
        'email',
        'user_id',
        'contact_no',
        'logo',
        'contact_person',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
