<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'child_id', 'date', 'status', 'comment', 'time', 'confirmed'
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
