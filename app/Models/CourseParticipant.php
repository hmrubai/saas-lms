<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'item_price',
        'paid_amount',
        'discount',
        'item_type',
        'is_trial_taken',
        'trial_expiry_date',
        'is_active'
    ];
}
