<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterVideo extends Model
{
    use HasFactory;
    protected $fillable = [
        "title" ,
        "title_bn",
        "video_code" ,
        "class_level_id" ,
        "subject_id" ,
        "chapter_id",
        "video_code" ,
        "author_name" ,
        "author_details",
        "description",
        "raw_url",
        "s3_url",
        "youtube_url",
        "download_url",
        "thumbnail" ,
        "duration",
        "price" ,
        "rating" ,
        "is_free",
        "sequence", 
        "is_active",
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean'
    ];
}
