<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageBenefitDetail extends Model
{
    use HasFactory;

    protected $table = 'package_benefit_details';

    protected $fillable = [
        'package_id',
        'benefit'
    ];

    protected $casts = [];
}
