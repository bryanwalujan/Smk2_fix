<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function getBarcodeAttribute()
    {
        if ($this->role === 'teacher') {
            return $this->teacher ? $this->teacher->barcode : null;
        } elseif ($this->role === 'student') {
            return $this->student ? $this->student->barcode : null;
        }
        return null;
    }
}