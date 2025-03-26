<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCheckpoints extends Model
{
    use HasFactory;

    protected $table = 'user_checkpoints';

    protected $fillable = [
        'user_id',
        'checkpoint_id',
        'group_id',
        'completed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

}

