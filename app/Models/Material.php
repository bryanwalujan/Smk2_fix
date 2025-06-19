<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['class_session_id', 'title', 'content', 'file_path'];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }
}