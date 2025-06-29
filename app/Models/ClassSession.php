<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $table = 'schedules';
    protected $fillable = ['teacher_id', 'classroom_id', 'subject_id', 'day', 'start_time', 'end_time'];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'day' => 'string',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'schedule_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'schedule_id', 'id');
    }
}