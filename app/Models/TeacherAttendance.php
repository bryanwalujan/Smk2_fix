<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $table = 'teacher_attendances';

    protected $fillable = [
        'teacher_id',
        'tanggal',
        'waktu_masuk',
        'waktu_pulang',
        'status',
        'metode_absen',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}