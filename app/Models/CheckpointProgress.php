<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use App\Models\User;
use App\Models\Checkpoint;

class CheckpointProgress extends Model
{
    protected $table = 'checkpoint_progress';

    protected $fillable = [
        'group_id',
        'user_id',
        'checkpoint_id',
        'completed_at',
        'score',
        'attempts'
    ];

    protected $casts = [
        'completed_at' => 'datetime'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }
}
