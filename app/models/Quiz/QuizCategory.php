<?php

namespace App\models\Quiz;
 
use Illuminate\Database\Eloquent\Model;

use App\models\Quiz\CategoryMain;
use App\models\dethi\Dethi;
use App\models\Quiz\TypeCategory;
class QuizCategory extends Model
{
    protected $table = "quiz_category";

    public function cate_main()
    {
        return $this->belongsTo(CategoryMain::class,'cate_id','id');
    }
    public function type_cate()
    {
        return $this->hasMany(TypeCategory::class,'cate_id','id');
    }
    public function dethi()
    {
        return $this->hasMany(Dethi::class,'subject','id')->orderBy('id','DESC')->limit(10);
    }
    public function saveCate($request)
    {
        $id = $request->id;
        $cat = CategoryMain::where('id',$request->cate_id)->first('slug');
        if($id != "" ){
            $query = QuizCategory::where([
                'id' => $id
             ])->first();
            if ($query) {
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->status = $request->status;
                $query->image = $request->image;
                $query->cate_id = $request->cate_id;
                $query->cate_slug = $cat->slug;
                $query->save();
            }else{
                $query = new QuizCategory();
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->status = $request->status;
                $query->cate_id = $request->cate_id;
                $query->cate_slug = $cat->slug;
                $query->save();
            }
            
        }else{
            $query = new QuizCategory();
            $query->name = $request->name;
            $query->slug = to_slug($request->name);
            $query->image = $request->image;
            $query->status = $request->status;
            $query->cate_id = $request->cate_id;
            $query->cate_slug = $cat->slug;
            $query->save();
            
        }
        return $query;
    }
}
