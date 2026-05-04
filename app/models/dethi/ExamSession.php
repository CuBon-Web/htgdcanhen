<?php

namespace App\models\dethi;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\models\product\Product;

class ExamSession extends Model
{
    protected $table = 'exam_sessions';
    protected $guarded = [];
    
    protected $casts = [
        'finished_at' => 'datetime',
        'started_at' => 'datetime',
        'part_results' => 'array'
    ];

    public function dethi()
    {
        return $this->belongsTo(Dethi::class, 'dethi_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'exam_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Customer::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Customer::class, 'teacher_id');
    }
    public function course()
    {
        return $this->belongsTo(Product::class, 'course_id');
    }
} 