<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsitePage extends Model
{
    use HasFactory;

    protected $table = 'website_pages';
    protected $fillable = [
        'page_title',
        'page_details',
        'page_banner',
        'organization_id',
    ];
}
