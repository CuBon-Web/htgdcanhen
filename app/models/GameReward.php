<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class GameReward extends Model
{
    protected $table = 'game_rewards';
    
    protected $fillable = [
        'name',
        'description',
        'image',
        'created_by',
        'status',
    ];
    
    public function configs()
    {
        return $this->hasMany(GameRewardConfig::class, 'reward_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(\App\Customer::class, 'created_by');
    }
}
