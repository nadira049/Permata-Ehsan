<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $fillable = [
        'name',
        'class_id',
        // add other fields as needed
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function progresses()
    {
        return $this->hasMany(Progress::class);
    }

    public function class()
    {
        return $this->belongsTo(\App\Models\Classroom::class, 'class_id');
    }
} 