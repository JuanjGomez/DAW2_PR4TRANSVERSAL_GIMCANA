<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Place extends Model
{
    use HasFactory;

    protected $table = 'places';

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'icon',
        'description'
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function favoriteByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_places');
    }
}
