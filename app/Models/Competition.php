<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'gimcana_id',
        'code',
        'created_by',
        'start_date',
        'status',
    ];

    public function gimcana()
    {
        return $this->belongsTo(Gimcana::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
