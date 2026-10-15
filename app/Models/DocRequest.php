<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'alumnus_id',
        'doc_type_ar',
        'doc_type_en',
        'status',
        'submitted_to_ministry_at',
        'ministry_status',
        'modification_reason_type',
        'modification_details',
        'delivered_to_student_at',
        'waiting_days',
        'file_path',
        'verification_code',
    ];

    protected $casts = [
        'submitted_to_ministry_at' => 'date',
        'delivered_to_student_at' => 'date',
    ];

    public function alumnus()
    {
        return $this->belongsTo(User::class, 'alumnus_id');
    }
}
