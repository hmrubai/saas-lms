<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerRequest extends Model
{
    use HasFactory;
    protected $table = 'consumer_requests';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'occupation',
        'organization_name',
        'organization_address',
        'nid_passport',
        'trade_license',
        'web_address',
        'post_code',
    ];
}
