<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules'; // Menentukan nama tabel

    protected $fillable = [
        'teacher_id',
        'classroom_id',
        'subject_id',
        'day',
        'start_time',
        'end_time',
        'created_by',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'class_session_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_session_id');
    }
}