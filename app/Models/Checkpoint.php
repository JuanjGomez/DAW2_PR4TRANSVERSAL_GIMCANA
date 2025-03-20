<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'gimcana_id',
        'place_id',
        'challenge',
        'clue',
        'order'
    ];

    public function gimcana()
    {
        return $this->belongsTo(Gimcana::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function groupCheckpoints()
    {
        return $this->hasMany(GroupCheckpoint::class);
    }
}
