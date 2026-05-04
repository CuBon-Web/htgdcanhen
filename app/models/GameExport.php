<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\models\Quiz\CategoryMain;
use App\models\Quiz\QuizCategory;
use App\models\GameResult;
class GameExport extends Model
{
    protected $fillable = [
        'teacher_id',
        'game_id',
        'grade_id',
        'subject_id',
        'game_name',
        'game_description',
        'questions_count',
        'html',
    ];
    public function teacher()
    {
        return $this->belongsTo(Customer::class, 'teacher_id');
    }
    public function grade()
    {
        return $this->belongsTo(CategoryMain::class, 'grade_id');
    }
    public function subject()
    {
        return $this->belongsTo(QuizCategory::class, 'subject_id');
    }
    public function gameResults()
    {
        return $this->hasMany(GameResult::class, 'game_export_id');
    }
}

