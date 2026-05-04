<?php

namespace App\models\Quiz;

use Illuminate\Database\Eloquent\Model;
use App\models\Quiz\QuizCategory;
use App\models\Quiz\TypeCategory;

class CategoryMain extends Model
{
    protected $table = "quiz_category_main";
    public function type_cate()
    {
        return $this->hasMany(TypeCategory::class,'cate_main_id','id');
    }
    public function category()
    {
        return $this->hasMany(QuizCategory::class,'cate_id','id')->orderBy('id',"DESC");
    }
    public function saveCate($request)
    {
        $id = $request->id;
        if($id != "" ){
            $query = CategoryMain::where([
                'id' => $id
             ])->first();
            if ($query) {
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->status = $request->status;
                $query->image = $request->image;
                $query->save();
            }else{
                $query = new CategoryMain();
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->status = $request->status;
                $query->save();
            }
            
        }else{
            $query = new CategoryMain();
            $query->name = $request->name;
            $query->slug = to_slug($request->name);
            $query->image = $request->image;
            $query->status = $request->status;
            $query->save();
            
        }
        return $query;
    }
}
