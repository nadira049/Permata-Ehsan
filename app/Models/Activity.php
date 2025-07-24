<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'name', 'description', 'date', 'status', 'year', 'image'
    ];

    public function images()
    {
        return $this->hasMany(ActivityImage::class);
    }
}
