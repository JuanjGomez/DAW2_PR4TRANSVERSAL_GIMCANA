<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Challenge;
use App\Models\Group;
use App\Models\Leaderboard;

class Competition extends Model
{
    protected $fillable = [
        'name',
        'challenge_id',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function leaderboard()
    {
        return $this->hasMany(Leaderboard::class);
    }
}
