<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'icon',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'place_tag');
    }

    public function favoriteByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_places');
    }
}
