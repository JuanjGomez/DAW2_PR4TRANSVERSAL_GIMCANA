<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Checkpoint;
use App\Models\Competition;
use App\Models\Group;

class Challenge extends Model
{
    protected $fillable = ['name', 'description', 'user_id'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function competitions()
    {
        return $this->hasMany(Competition::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
