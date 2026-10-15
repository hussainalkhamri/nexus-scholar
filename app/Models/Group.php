<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'department',
        'supervisor',
        'supervisor_id',
        'stage',
        'status',
        'progress',
        'days_in_stage',
        'semester',
        'plagiarism_percent',
        'grade',
        'defense_committee',
        'defense_date',
    ];

    public function students()
    {
        return $this->belongsToMany(User::class, 'group_student', 'group_id', 'student_id');
    }

    public function supervisorUser()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }
}
