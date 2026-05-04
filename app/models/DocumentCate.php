<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class DocumentCate extends Model
{
    protected $table = "document_cate";
    public function document()
    {
        return $this->hasMany(Document::class,'cate_id','id')->orderBy('order_id','ASC');
    }
    public function saveCate($request)
    {
        $id = $request->id;
        if($id != "" ){
            $query = DocumentCate::where([
                'id' => $id
             ])->first();
            if ($query) {
                $query->name = $request->name;
                $query->order_id = $request->order_id;
                $query->slug = to_slug($request->name);
                $query->status = $request->status;
                $query->save();
            }else{
                $query = new DocumentCate();
                $query->name = $request->name;
                $query->order_id = $request->order_id;
                $query->slug = to_slug($request->name);
                $query->status = $request->status;
                $query->save();
            }
            
        }else{
            $query = new DocumentCate();
            $query->name = $request->name;
            $query->order_id = $request->order_id;
            $query->slug = to_slug($request->name);
            $query->status = $request->status;
            $query->save();
            
        }
        return $query;
    }
}
