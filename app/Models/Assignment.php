<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['class_session_id', 'title', 'description', 'deadline'];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}