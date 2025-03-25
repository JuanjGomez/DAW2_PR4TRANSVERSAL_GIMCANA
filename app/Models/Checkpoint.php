<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Checkpoint extends Model
{
    use HasFactory;

    protected $table = 'checkpoints';

    protected $fillable = [
        'name',
        'gimcana_id',
        'place_id',
        'challenge',
        'clue',
        'order',
    ];

    public function gimcana()
    {
        return $this->belongsTo(Gimcana::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

}
