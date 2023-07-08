<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageType extends Model
{
    use HasFactory;

    protected $table = 'package_types';

    protected $fillable = [
        'name',
        'price',
        'limit',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
