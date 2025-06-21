<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeacherClassroomSubject extends Pivot
{
    protected $table = 'teacher_classroom_subject';

    protected $fillable = ['teacher_id', 'classroom_id', 'subject_name'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}