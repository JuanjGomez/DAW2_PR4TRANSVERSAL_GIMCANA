<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'challenge_id',
        'code',
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
