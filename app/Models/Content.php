<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'title_bn',
        'category_id',
        'gp_product_id',
        'youtube_url',
        'description',
        'thumbnail',
        'icon',
        'number_of_enrolled',
        'regular_price',
        'sale_price',
        'discount_percentage',
        'rating',
        'is_active',
        'is_free',
        'sequence',
        'appeared_from',
        'appeared_to'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean',
    ];
}
