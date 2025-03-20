<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'competition_id',
        'leader_id',
        'current_checkpoint_id',
    ];

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(GroupCheckpoint::class);
    }
}
