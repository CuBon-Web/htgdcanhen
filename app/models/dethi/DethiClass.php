<?php

namespace App\models\dethi;

use Illuminate\Database\Eloquent\Model;

class DethiClass extends Model
{
    protected $table = 'dethi_classes';
    protected $guarded = [];

    /**
     * Relationship: Thuộc về một đề thi
     */
    public function dethi()
    {
        return $this->belongsTo(Dethi::class, 'dethi_id');
    }

    /**
     * Relationship: Thuộc về một lớp học
     */
    public function schoolClass()
    {
        return $this->belongsTo(\App\models\SchoolClass::class, 'class_id');
    }
}
