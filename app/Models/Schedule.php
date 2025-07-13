<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'teacher_id',
        'classroom_id',
        'subject_id',
        'day',
        'start_time',
        'end_time',
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

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class)
            ->where('teacher_id', $this->teacher_id)
            ->where('classroom_id', $this->classroom_id)
            ->where('subject_id', $this->subject_id)
            ->where('day_of_week', $this->day)
            ->where('start_time', $this->start_time)
            ->where('end_time', $this->end_time);
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'schedule_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'schedule_id');
    }
}