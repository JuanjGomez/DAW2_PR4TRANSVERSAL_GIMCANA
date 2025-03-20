<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Challenge;
use App\Models\Place;
use App\Models\CheckpointProgress;

class Checkpoint extends Model
{
    protected $fillable = [
        'challenge_id',
        'place_id',
        'clue',
        'test',
        'order'
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function progress()
    {
        return $this->hasMany(CheckpointProgress::class);
    }
}
