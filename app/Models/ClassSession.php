<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $table = 'class_sessions';

    protected $fillable = [
        'teacher_id',
        'classroom_id',
        'subject_id',
        'day_of_week',
        'date',
        'start_time',
        'end_time',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'day_of_week' => 'string',
        'date' => 'date',
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
        return $this->hasMany(Material::class, 'class_session_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_session_id', 'id');
    }

    public function getMeetingNumberAttribute()
    {
        $previousSessions = self::where('teacher_id', $this->teacher_id)
            ->where('classroom_id', $this->classroom_id)
            ->where('subject_id', $this->subject_id)
            ->where('day_of_week', $this->day_of_week)
            ->where('start_time', $this->start_time)
            ->where('end_time', $this->end_time)
            ->where('date', '<=', $this->date)
            ->orderBy('date')
            ->get();
        return $previousSessions->pluck('id')->search($this->id) + 1;
    }
}