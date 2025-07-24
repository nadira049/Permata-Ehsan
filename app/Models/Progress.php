<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;
    protected $table = 'progress';
    protected $fillable = [
        'child_id', 'date', 'progress', 'level', 'confirmed',
    ];
    public function child()
    {
        return $this->belongsTo(Child::class);
    }
} 