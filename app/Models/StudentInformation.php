<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInformation extends Model
{
    use HasFactory;
    protected $table = 'student_informations';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'username',
        'contact_no',
        'student_code',
        'organization_slug',
        'device_id',
        'alternative_contact_no',
        'gender',
        'blood_group',
        'bio',
        'father_name',
        'mother_name',
        'religion',
        'marital_status',
        'date_of_birth',
        'profession',
        'current_address',
        'permanent_address',
        'interests',
        'division_id',
        'city_id',
        'area_id',
        'nid_no',
        'birth_certificate_no',
        'passport_no',
        'image',
        'intro_video',
        'status',
        'is_foreigner',
        'is_active',
        'rating',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
