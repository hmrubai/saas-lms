<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory;

    protected $table = 'website_settings';
    protected $fillable = [
        'banner',
        'contact_number',
        'hotline_number',
        'email',
    ];
}
