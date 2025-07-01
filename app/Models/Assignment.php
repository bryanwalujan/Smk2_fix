<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_id', 'title', 'description', 'deadline'];

    protected $dates = ['deadline'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}