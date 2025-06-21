<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $fillable = ['teacher_id', 'classroom_id', 'subject_name', 'day_of_week', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'day_of_week' => 'string', 
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}