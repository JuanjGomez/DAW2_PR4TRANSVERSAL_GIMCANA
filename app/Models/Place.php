<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\Checkpoint;
use App\Models\User;

class Place extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'description', 'icon'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorite')
                    ->withTimestamps();
    }
}
