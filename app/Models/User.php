<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Role;
use App\Models\Challenge;
use App\Models\Group;
use App\Models\Place;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function memberGroups()
    {
        return $this->belongsToMany(Group::class, 'group_member')
                    ->withTimestamp('joined_at');
    }

    public function favoritePlaces()
    {
        return $this->belongsToMany(Place::class, 'user_favorite')
                    ->withTimestamps();
    }
}
