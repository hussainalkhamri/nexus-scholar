<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'name_ar', 'name_en', 'email', 'academic_id', 'password', 'role', 'phone', 'department', 'last_active_at', 'experience', 'conferences', 'achievements', 'linkedin_url', 'headline', 'education', 'languages', 'certifications'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_active_at' => 'datetime',
            'experience' => 'array',
            'conferences' => 'array',
            'achievements' => 'array',
            'education' => 'array',
            'languages' => 'array',
            'certifications' => 'array',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function ($user) {
            if ($user->role === 'alumnus') {
                $user->alumniProfile()->firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'graduation_year' => date('Y'),
                        'employed' => false,
                        'points' => 100,
                    ]
                );
            }
        });
    }

    public function isBirthdayToday(): bool
    {
        return $this->alumniProfile ? $this->alumniProfile->isTodayBirthday() : false;
    }

    public function alumniProfile()
    {
        return $this->hasOne(AlumniProfile::class);
    }

    public function docRequests()
    {
        return $this->hasMany(DocRequest::class, 'alumnus_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_student', 'student_id', 'group_id');
    }

    public function supervisedGroups()
    {
        return $this->hasMany(Group::class, 'supervisor_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function projectFiles()
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function incentives()
    {
        return $this->belongsToMany(Incentive::class, 'incentive_user')->withPivot('status')->withTimestamps();
    }
}
