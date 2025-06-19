<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['nip', 'name', 'barcode', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'teacher_classroom_subject')
                    ->withPivot('subject_name');
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }
}