<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    protected $fillable = [
        'title', 'content', 'file_path', 'status', 'year'
    ];
}
