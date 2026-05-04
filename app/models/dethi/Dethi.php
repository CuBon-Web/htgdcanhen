<?php

namespace App\models\dethi;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\models\product\Product;
use App\models\Bill\BillDethi;
use App\models\Quiz\QuizCategory;
use App\models\Quiz\CategoryMain;
use App\models\Quiz\TypeCategory;
use App\models\GameResult;
class Dethi extends Model
{
    protected $table = 'dethi';
    protected $guarded = [];

    public function parts()
    {
        return $this->hasMany(DethiPart::class, 'dethi_id');
    }
    public function game_results()
    {
        return $this->hasMany(GameResult::class, 'game_id');
    }
    public function sessions()
    {
        return $this->hasMany(ExamSession::class, 'dethi_id');
    }
    public function typeCategory()
    {
        return $this->belongsTo(TypeCategory::class, 'cate_type_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'created_by');
    }
    public function course()
    {
        return $this->belongsTo(Product::class, 'course_id');
    }
    public function bill_dethi()
    {
        return $this->hasMany(BillDethi::class, 'dethi_id');
    }

    public function folder()
    {
        return $this->belongsTo(ExamFolder::class, 'folder_id');
    }

    /**
     * Relationship: Đề thi có nhiều lớp được phép làm bài
     */
    public function allowedClasses()
    {
        return $this->hasMany(DethiClass::class, 'dethi_id');
    }

    /**
     * Relationship: Đề thi thuộc môn học
     */
    public function subjectCategory()
    {
        return $this->belongsTo(QuizCategory::class, 'subject', 'id');
    }

    /**
     * Relationship: Đề thi thuộc cấp học
     */
    public function gradeCategory()
    {
        return $this->belongsTo(CategoryMain::class, 'grade', 'id');
    }

    /**
     * Kiểm tra xem học sinh có quyền làm đề thi này không
     * @param string $classCode - Mã lớp của học sinh
     * @return bool
     */
    public function canStudentAccess($classCode = null)
    {
        // Nếu access_type là 'all', tất cả đều được làm
        if ($this->access_type === 'all') {
            return true;
        }

        // Nếu access_type là 'class', kiểm tra lớp bằng class_code
        if ($this->access_type === 'class' && $classCode) {
            // Lấy class_id từ class_code
            $class = \App\models\SchoolClass::where('class_code', $classCode)->first();
            if ($class) {
                return $this->allowedClasses()->where('class_id', $class->id)->exists();
            }
            return false;
        }

        // Nếu access_type là 'time_limited', kiểm tra thời gian
        if ($this->access_type === 'time_limited') {
            $now = now();
            if ($this->start_time && $this->end_time) {
                return $now->between($this->start_time, $this->end_time);
            }
        }

        return false;
    }

    /**
     * Kiểm tra học sinh có quyền truy cập đề thi không (hỗ trợ nhiều lớp)
     * @param array|string $classCodes Mảng class codes hoặc một class code
     * @return bool
     */
    public function canStudentAccessMultiple($classCodes)
    {
        // Nếu access_type là 'all', tất cả đều được làm
        if ($this->access_type === 'all') {
            return true;
        }

        // Nếu access_type là 'class', kiểm tra lớp
        if ($this->access_type === 'class') {
            // Chuyển đổi thành mảng nếu là string
            if (is_string($classCodes)) {
                $classCodes = [$classCodes];
            }
            
            if (empty($classCodes)) {
                return false;
            }

            // Lấy tất cả class_id từ class_code
            $classes = \App\models\SchoolClass::whereIn('class_code', $classCodes)->get();
            if ($classes->isEmpty()) {
                return false;
            }

            $classIds = $classes->pluck('id')->toArray();
            
            // Kiểm tra xem có lớp nào trong danh sách được phép không
            return $this->allowedClasses()->whereIn('class_id', $classIds)->exists();
        }

        // Nếu access_type là 'time_limited', kiểm tra thời gian
        if ($this->access_type === 'time_limited') {
            $now = now();
            if ($this->start_time && $this->end_time) {
                return $now->between($this->start_time, $this->end_time);
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem đề thi có còn trong thời gian làm bài không
     * @return bool
     */
    public function isWithinTimeLimit()
    {
        if ($this->access_type !== 'time_limited') {
            return true;
        }

        $now = now();
        if ($this->start_time && $this->end_time) {
            return $now->between($this->start_time, $this->end_time);
        }

        return false;
    }
} 
