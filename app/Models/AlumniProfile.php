<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AlumniProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'graduation_year',
        'birth_place',
        'birth_date',
        'enrollment_year',
        'graduation_session',
        'prev_qualification_type',
        'prev_qualification_year',
        'prev_qualification_issuer',
        'prev_qualification_gpa',
        'workplace',
        'position',
        'employed',
        'skills',
        'points',
        'linkedin_url',
    ];

    protected $casts = [
        'employed' => 'boolean',
        'skills' => 'array',
        'birth_date' => 'date',
    ];

    public function isTodayBirthday(): bool
    {
        if (!$this->birth_date) {
            return false;
        }
        return $this->birth_date->format('m-d') === now()->format('m-d');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
