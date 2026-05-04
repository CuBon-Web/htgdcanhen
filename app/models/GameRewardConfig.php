<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\dethi\Dethi;

class GameRewardConfig extends Model
{
    protected $table = 'game_reward_configs';
    
    protected $fillable = [
        'game_id',
        'reward_id',
        'min_percentage',
        'max_percentage',
        'quantity',
        'priority',
    ];
    
    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
    ];
    
    public function game()
    {
        return $this->belongsTo(Dethi::class, 'game_id');
    }
    
    public function reward()
    {
        return $this->belongsTo(GameReward::class, 'reward_id');
    }
    
    /**
     * Kiểm tra xem phần trăm có thỏa mãn điều kiện không
     */
    public function matchesPercentage($percentage)
    {
        if ($percentage < $this->min_percentage) {
            return false;
        }
        
        if ($this->max_percentage !== null && $percentage > $this->max_percentage) {
            return false;
        }
        
        return true;
    }
}
