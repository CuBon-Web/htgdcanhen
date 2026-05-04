<?php

namespace App\models\dethi;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $table = 'exam_answers';
    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function question()
    {
        return $this->belongsTo(DethiQuestion::class, 'question_id');
    }

    public function grader()
    {
        return $this->belongsTo(\App\models\Customer::class, 'graded_by');
    }
} 