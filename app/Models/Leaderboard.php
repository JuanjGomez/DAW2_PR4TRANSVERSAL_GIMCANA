<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Competition;
use App\Models\Group;

class Leaderboard extends Model
{
    protected $fillable = [
        'competition_id',
        'group_id',
        'total_score',
        'total_time_seconds'
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
