<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\dethi\Dethi;
use App\Customer;

class GameAttempt extends Model
{
    protected $fillable = [
        'game_id',
        'student_id',
        'token',
        'ip_address',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Dethi::class, 'game_id');
    }

    public function student()
    {
        return $this->belongsTo(Customer::class, 'student_id');
    }
}

