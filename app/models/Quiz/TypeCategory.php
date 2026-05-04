<?php

namespace App\models\Quiz;

use Illuminate\Database\Eloquent\Model;
use App\models\Quiz\QuizCategory;


class TypeCategory extends Model
{
    protected $table = "quiz_type_category";
    public function cate_main()
    {
        return $this->belongsTo(CategoryMain::class,'cate_main_id','id');
    }
    public function cate()
    {
        return $this->belongsTo(QuizCategory::class,'cate_id','id');
    }
    public function saveCate($request)
    {
        $id = $request->id;
        if($id != "" ){
            $query = TypeCategory::where([
                'id' => $id
             ])->first();
            if ($query) {
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->cate_id = $request->cate_id;
                $query->cate_main_id = $request->cate_main_id;
                $query->status = $request->status;
                $query->save();
            }else{
                $query = new TypeCategory();
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->cate_id = $request->cate_id;
                $query->cate_main_id = $request->cate_main_id;
                $query->status = $request->status;
                $query->save();
            }
            
        }else{
            $query = new TypeCategory();
            $query->name = $request->name;
            $query->slug = to_slug($request->name);
            $query->cate_id = $request->cate_id;
            $query->cate_main_id = $request->cate_main_id;
            $query->status = $request->status;
            $query->save();
            
        }
        return $query;
    }
}
