<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorZoomLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'live_link',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
