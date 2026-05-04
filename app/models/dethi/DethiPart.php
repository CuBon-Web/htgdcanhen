<?php

namespace App\models\dethi;

use Illuminate\Database\Eloquent\Model;

class DethiPart extends Model
{
    protected $table = 'dethi_parts';
    protected $guarded = [];

    public function dethi()
    {
        return $this->belongsTo(Dethi::class, 'dethi_id');
    }

    public function questions()
    {
        return $this->hasMany(DethiQuestion::class, 'dethi_part_id');
    }   
} 