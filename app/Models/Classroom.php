<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = ['level', 'major', 'class_code', 'full_name'];

    // Computed attribute for full_name
    public function getFullNameAttribute()
    {
        // Jika full_name di database kosong, gunakan level, major, dan class_code
        return $this->attributes['full_name'] ?? "{$this->level} {$this->major} {$this->class_code}";
    }

    // Relasi ke schedules
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Relasi ke students
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Relasi many-to-many ke teachers melalui tabel teacher_classroom_subject
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_classroom_subject', 'classroom_id', 'teacher_id')
                    ->withPivot('subject_name');
    }
}