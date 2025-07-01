<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    protected $table = 'class_sessions';

    protected $fillable = [
        'schedule_id',
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

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

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

    public function getMeetingNumberAttribute()
    {
        $previousSessions = self::where('schedule_id', $this->schedule_id)
            ->where('date', '<=', $this->date)
            ->orderBy('date')
            ->get();
        return $previousSessions->pluck('id')->search($this->id) + 1;
    }
}