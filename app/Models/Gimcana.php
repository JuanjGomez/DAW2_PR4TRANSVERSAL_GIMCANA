<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gimcana extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }
}
