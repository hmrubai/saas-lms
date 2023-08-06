<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorInformation extends Model
{
    use HasFactory;

    protected $table = 'mentor_informations';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'username',
        'education',
        'institute',
        'contact_no',
        'mentor_code',
        'organization_slug',
        'device_id',
        'referral_code',
        'referred_code',
        'alternative_contact_no',
        'gender',
        'bio',
        'father_name',
        'mother_name',
        'religion',
        'marital_status',
        'date_of_birth',
        'profession',
        'current_address',
        'permanent_address',
        'division_id',
        'district_id',
        'city_id',
        'area_id',
        'nid_no',
        'birth_certificate_no',
        'passport_no',
        'interests',
        'image',
        'intro_video',
        'status',
        'is_foreigner',
        'is_life_couch',
        'is_host_staff',
        'is_host_certified',
        'is_active',
        'rating',
        'approval_date',
        'host_rank_number'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_foreigner' => 'boolean',
        'is_life_couch' => 'boolean',
        'is_host_staff' => 'boolean',
        'is_host_certified' => 'boolean',
    ];
}
