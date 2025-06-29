<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['schedule_id', 'title', 'description', 'deadline'];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'schedule_id', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}