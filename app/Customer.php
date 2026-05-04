<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use Notifiable;
    protected $table = 'customer';
    protected $fillable = [
        'id', 'name', 'email', 'address', 'phone', 'created_at', 'updated_at','status','google_id','password','facebook_id','class_code'
    ];

    protected $casts = [
        'class_code' => 'array', // Tự động chuyển đổi JSON thành array và ngược lại
    ];

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.User.1';
    }

    /**
     * Lấy tất cả các lớp học của học sinh từ class_code (JSON)
     * Helper method
     */
    public function getSchoolClasses()
    {
        $classCodes = $this->class_code ?? [];
        if (empty($classCodes) || !is_array($classCodes)) {
            return collect();
        }
        return \App\models\SchoolClass::whereIn('class_code', $classCodes)->get();
    }

    /**
     * Accessor để lấy danh sách lớp học (tương thích với $profile->schoolClasses)
     */
    public function getSchoolClassesAttribute()
    {
        return $this->getSchoolClasses();
    }

    /**
     * Get school class by class_code (lấy lớp đầu tiên)
     */
    public function schoolClass()
    {
        $classCodes = $this->class_code ?? [];
        if (empty($classCodes) || !is_array($classCodes)) {
            return null;
        }
        return \App\models\SchoolClass::where('class_code', $classCodes[0])->first();
    }

    /**
     * Accessor để lấy lớp đầu tiên (tương thích với $profile->schoolClass)
     */
    public function getSchoolClassAttribute()
    {
        return $this->schoolClass();
    }

    /**
     * Get class names of student (tất cả các lớp)
     */
    public function getClassNamesAttribute()
    {
        $classes = $this->getSchoolClasses();
        return $classes->pluck('class_name')->toArray();
    }

    /**
     * Get class name of student (lấy tên lớp đầu tiên)
     */
    public function getClassNameAttribute()
    {
        $class = $this->schoolClass();
        return $class ? $class->class_name : null;
    }

    /**
     * Kiểm tra học sinh có thuộc lớp nào không
     */
    public function hasClass($classCode)
    {
        $classCodeUpper = strtoupper($classCode);
        $classCodes = $this->class_code ?? [];
        if (!is_array($classCodes)) {
            return false;
        }
        return in_array($classCodeUpper, array_map('strtoupper', $classCodes));
    }

    /**
     * Lấy tất cả class_code của học sinh (từ JSON) - alias cho class_code
     */
    public function getClassCodesAttribute()
    {
        // Truy cập trực tiếp vào attributes để tránh infinite loop
        $classCodeJson = $this->attributes['class_code'] ?? null;
        if ($classCodeJson === null) {
            return [];
        }
        $classCodes = json_decode($classCodeJson, true);
        return is_array($classCodes) ? $classCodes : [];
    }

    /**
     * Set class codes (lưu dưới dạng JSON)
     */
    public function setClassCodesAttribute($value)
    {
        if (is_array($value)) {
            // Loại bỏ các giá trị rỗng và chuyển thành chữ hoa
            $value = array_filter(array_map(function($code) {
                return strtoupper(trim($code));
            }, $value));
            $this->attributes['class_code'] = !empty($value) ? json_encode(array_values($value)) : null;
        } else {
            $this->attributes['class_code'] = null;
        }
    }
}
