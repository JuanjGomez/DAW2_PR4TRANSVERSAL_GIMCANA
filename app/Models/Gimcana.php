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
<<<<<<< HEAD
        'max_users_per_group'
=======
        'max_users_per_group',
        'status',
>>>>>>> 4bf6ee045fabac475af51b364c62b4396661ab98
    ];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

}
