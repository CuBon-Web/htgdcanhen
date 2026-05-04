<?php

namespace App\models\dethi;

use Illuminate\Database\Eloquent\Model;

class DethiQuestion extends Model
{
    protected $table = 'dethi_questions';
    protected $guarded = [];

    public function part()
    {
        return $this->belongsTo(DethiPart::class, 'dethi_part_id');
    }

    public function answers()
    {
        return $this->hasMany(DethiAnswer::class, 'dethi_question_id','id');
    }
} 
?>