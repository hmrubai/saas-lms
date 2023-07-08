<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalNotice extends Model
{
    use HasFactory;

    protected $table = 'promotional_notices';

    protected $fillable = [
        'title',
        'description',
        'navigation_link',
        'feature_image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
