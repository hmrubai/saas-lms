<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'school_id',
        'package_id',
        'is_promo_applied',
        'promo_id',
        'payable_amount',
        'paid_amount',
        'discount_amount',
        'currency',
        'transaction_id',
        'payment_type',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'is_promo_applied' => 'boolean',
    ];
}
