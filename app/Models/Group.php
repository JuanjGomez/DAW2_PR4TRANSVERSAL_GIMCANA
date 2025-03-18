<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\CheckpointProgress;
use App\Models\Leaderboard;

class Group extends Model
{
    protected $fillable = ['name', 'user_id', 'challenge_id'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_member')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function competitions()
    {
        return $this->belongsToMany(Competition::class);
    }

    public function checkpointProgress()
    {
        return $this->hasMany(CheckpointProgress::class);
    }

    public function leaderboardEntries()
    {
        return $this->hasMany(Leaderboard::class);
    }
}
