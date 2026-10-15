<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incentive extends Model
{
    use HasFactory;

    protected $fillable = [
        'kind',
        'title_ar',
        'title_en',
        'detail_ar',
        'detail_en',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'incentive_user')->withPivot('status')->withTimestamps();
    }
}
