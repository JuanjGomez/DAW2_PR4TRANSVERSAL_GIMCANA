<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Place;
class Tag extends Model
{
    protected $fillable = ['name'];

    public function places()
    {
        return $this->belongsToMany(Place::class);
    }
}
