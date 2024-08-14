<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'title_bn',
        'category_id',
        'gp_product_id',
        'course_type_id',
        'youtube_url',
        'description',
        'thumbnail',
        'icon',
        'number_of_enrolled',
        'regular_price',
        'sale_price',
        'discount_percentage',
        'rating',
        'has_life_coach',
        'is_active',
        'is_free',
        'sequence',
        'appeared_from',
        'appeared_to',
        'feed_to_homepage'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'has_life_coach' => 'boolean',
        'feed_to_homepage' => 'boolean'
    ];
}
