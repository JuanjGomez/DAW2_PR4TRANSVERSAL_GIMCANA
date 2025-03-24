<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ChallengeAnswers extends Model
{
    use HasFactory;

    protected $table = 'challenge_answers';

    protected $fillable = [
        'checkpoint_id',
        'answer',
        'is_correct',
    ];

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }
}
