<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
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
        return $this->hasMany(ClassSession::class, 'teacher_id', 'teacher_id')
            ->where('class_sessions.classroom_id', $this->classroom_id)
            ->where('class_sessions.subject_id', $this->subject_id)
            ->where('class_sessions.day_of_week', $this->day)
            ->where('class_sessions.start_time', $this->start_time)
            ->where('class_sessions.end_time', $this->end_time);
    }
}