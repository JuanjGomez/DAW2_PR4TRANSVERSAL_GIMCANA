<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkpoint_id',
        'answer',
        'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean'
    ];

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }
} 