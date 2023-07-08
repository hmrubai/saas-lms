<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correction extends Model
{
    use HasFactory;

    protected $table = 'corrections';

    protected $fillable = [
        'user_id',
        'admin_id',
        'school_id',
        'topic_id',
        'package_id',
        'package_type_id',
        'expert_id',
        'deadline',
        'is_accepted',
        'accepted_date',
        'is_seen_by_expert',
        'is_seen_by_student',
        'is_student_resubmited',
        'status',
        'student_correction',
        'expert_correction_note',
        'expert_correction_feedback',
        'grade',
        'student_rewrite',
        'expert_final_note',
        'student_correction_date',
        'expert_correction_date',
        'completed_date',
        'student_resubmission_date',
        'expert_final_note_date',
        'rating',
        'rating_note'
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
        'is_seen_by_expert' => 'boolean',
        'is_seen_by_student' => 'boolean',
        'is_student_resubmited' => 'boolean'
    ];
}
