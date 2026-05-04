<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\models\dethi\DethiClass;

class SchoolClass extends Model
{
    protected $table = 'school_classes';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Giáo viên chủ nhiệm
     */
    public function homeroomTeacher()
    {
        return $this->belongsTo(Customer::class, 'homeroom_teacher_id');
    }

    /**
     * Lấy tất cả học sinh của lớp (từ JSON class_code)
     * Không phải relationship, chỉ là method helper
     */
    public function getStudents()
    {
        if (!$this->class_code) {
            return collect();
        }
        return Customer::whereJsonContains('class_code', $this->class_code)->get();
    }
    
    /**
     * Get students count
     */
    public function getStudentsCountAttribute()
    {
        if (!$this->class_code) {
            return 0;
        }
        return Customer::whereJsonContains('class_code', $this->class_code)->count();
    }
    
    /**
     * Accessor để lấy danh sách học sinh (tương thích với with('students'))
     */
    public function getStudentsAttribute()
    {
        return $this->getStudents();
    }

    /**
     * Relationship: Lớp học có thể làm nhiều đề thi
     */
    public function dethis()
    {
        return $this->hasMany(DethiClass::class, 'class_id');
    }

    /**
     * Get full class display name
     */
    public function getFullNameAttribute()
    {
        return $this->class_name . ($this->school_year ? " (NH {$this->school_year})" : '');
    }

    /**
     * Scope: Chỉ lớp đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
