<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'gimcana_id',
        'challenge',
        'clue',
        'order',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function gimcana()
    {
        return $this->belongsTo(Gimcana::class);
    }
}
