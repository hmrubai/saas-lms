<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicConsume extends Model
{
    use HasFactory;
    
    protected $table = 'topic_consumes';

    protected $fillable = [
        'payment_id',
        'user_id',
        'school_id',
        'package_id',
        'package_type_id',
        'balance',
        'consumme',
        'expiry_date',
        'is_fully_consumed'
    ];

    protected $casts = [
        'is_fully_consumed' => 'boolean',
    ];
}
