<?php

namespace App\models\dethi;

use Illuminate\Database\Eloquent\Model;

class DethiAnswer extends Model
{
    protected $table = 'dethi_answers';
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(DethiQuestion::class, 'dethi_questions_id');
    }
} 