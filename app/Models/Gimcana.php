<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Gimcana extends Model
{
    use HasFactory;

    protected $table = 'gimcanas';

    protected $fillable = [
        'name',
        'description',
        'max_groups',
        'max_users_per_group',
        'status',
    ];

    protected $with = ['groups.members'];
    
    protected $appends = ['current_players'];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function getCurrentPlayersAttribute()
    {
        return $this->groups->sum(function ($group) {
            return $group->members->count();
        });
    }
}
