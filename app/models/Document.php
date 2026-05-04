<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = "document";
    public function cate()
    {
        return $this->hasOne(DocumentCate::class,'id','cate_id');
    }
    public function saveDocument($request)
    {
    	$id = $request->id;
        $cate = DocumentCate::where([
            'id' => $request->cate_id
            ])->first();
        if($id != ""){
            $query = Document::where([
                'id' => $id
             ])->first();
            
            if ($query) {
                $query->name = $request->name;
                $query->order_id = $request->order_id;
                $query->slug = to_slug($request->name);
                $query->pdf = $request->pdf;
                $query->docs = $request->docs;
                $query->price = $request->price;
                $query->content = json_encode($request->content);
                $query->description = json_encode($request->description);
                $query->status = $request->status;
                $query->image = ($request->image);
                $query->cate_id = $request->cate_id;
                $query->cate_slug = $cate->slug;
                $query->save();
            }else{
                $query = new Document();
                $query->name = $request->name;
                $query->order_id = $request->order_id;
                $query->pdf = $request->pdf;
                $query->docs = $request->docs;
                $query->price = $request->price;
                $query->slug = to_slug($request->name);
                $query->content = json_encode($request->content);
                $query->description = json_encode($request->description);
                $query->status = $request->status;
                $query->image = ($request->image);
                $query->cate_id = $request->cate_id;
                $query->cate_slug = $cate->slug;
                $query->save();
            }
            
        }else{
                $query = new Document();
                $query->name = $request->name;
                $query->order_id = $request->order_id;
                $query->pdf = $request->pdf;
                $query->docs = $request->docs;
                $query->price = $request->price;
                $query->slug = to_slug($request->name);
                $query->content = json_encode($request->content);
                $query->description = json_encode($request->description);
                $query->status = $request->status;
                $query->image = ($request->image);
                $query->cate_id = $request->cate_id;
                $query->cate_slug = $cate->slug;
                $query->save();
            
        }
        return $query;
    }
}
