<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;
    
    protected $table = 'payment_details';

    protected $fillable = [
        'user_id',
        'school_id',
        'package_id',
        'package_type_id',
        'payment_id',
        'unit_price',
        'quantity',
        'total'
    ];

    protected $casts = [];
}
