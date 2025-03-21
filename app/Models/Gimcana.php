<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gimcana extends Model
{
    use HasFactory;
    protected $table = 'gimcanas';

    protected $fillable = [
        'name',
        'description',
        'max_groups',
        'max_users_per_group'
    ];

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
