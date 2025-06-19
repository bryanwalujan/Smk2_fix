<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $table = 'student_attendances';

    protected $fillable = [
        'student_id',
        'tanggal',
        'waktu_masuk',
        'waktu_pulang',
        'status',
        'metode_absen',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}