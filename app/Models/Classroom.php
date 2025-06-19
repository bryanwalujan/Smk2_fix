<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['level', 'major', 'class_code'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_classroom_subject')
                    ->withPivot('subject_name');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function getFullNameAttribute()
    {
        return "Kelas {$this->level} {$this->major} {$this->class_code}";
    }
}