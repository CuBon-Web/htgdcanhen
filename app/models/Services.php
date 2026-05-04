<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\ServiceCate;
class Services extends Model
{
    protected $table = "services";
    public function cate()
    {
        return $this->hasOne(ServiceCate::class,'id','cate_id');
    }
    public function saveServices($request)
    {
    	$id = $request->id;
        $cate = ServiceCate::where([
            'id' => $request->cate_id
            ])->first();
        if($id != ""){
            $query = Services::where([
                'id' => $id
             ])->first();
            
            if ($query) {
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->content = json_encode($request->content);
                $query->description = json_encode($request->description);
                $query->otherser = json_encode($request->otherser);
                $query->status = $request->status;
                $query->image = $request->image;
                $query->cate_id = 0;
                $query->cate_slug = "";
                $query->save();
            }else{
                $query = new Services();
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->content = json_encode($request->content);
                $query->description = json_encode($request->description);
                $query->otherser = json_encode($request->otherser);
                $query->status = $request->status;
                $query->image = $request->image;
                $query->cate_id = 0;
                $query->cate_slug = "";
                $query->save();
            }
            
        }else{
                $query = new Services();
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->content = json_encode($request->content);
                $query->description = json_encode($request->description);
                $query->otherser = json_encode($request->otherser);
                $query->status = $request->status;
                $query->image = $request->image;
                $query->cate_id = 0;
                $query->cate_slug = "";
                $query->save();
            
        }
        return $query;
    }
}
