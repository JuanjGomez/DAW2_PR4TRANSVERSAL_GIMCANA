<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'name',
        'current_checkpoint',
        'gimcana_id',
    ];

    public function gimcana()
    {
        return $this->belongsTo(Gimcana::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members');
    }

}
