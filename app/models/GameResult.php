<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\dethi\Dethi;

class GameResult extends Model
{
    protected $fillable = [
        'game_export_id',
        'game_id',
        'student_id',
        'student_name',
        'student_email',
        'correct_count',
        'total_questions',
        'percentage',
        'status',
        'earned_rewards',
        'ip_address',
    ];

    protected $casts = [
        'earned_rewards' => 'array',
        'percentage' => 'float',
    ];

    public function game()
    {
        return $this->belongsTo(Dethi::class, 'game_id');
    }
}

