<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionRating extends Model
{
    use HasFactory;
    
    protected $table = 'correction_ratings';

    protected $fillable = [
        'expert_id',
        'rating',
        'total_rating',
        'total_correction',
        'rating_avg'
    ];

    protected $casts = [];

}
