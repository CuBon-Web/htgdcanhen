<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ReviewCus extends Model
{
    protected $table = "reviewcus";
    public function saveReviewCus($request)
    {
    	$id = $request->id;
        if($id != ""){
            $query = ReviewCus::where([
                'id' => $id
             ])->first();
            if ($query) {
                $query->name = ($request->name);
                $query->position = ($request->position);
                $query->content = ($request->content);
                $query->status = $request->status;
                $query->avatar = $request->avatar;
                $query->save();
            }else{
                $query = new ReviewCus();
                $query->name = ($request->name);
                $query->position = ($request->position);
                $query->content = ($request->content);
                $query->status = $request->status;
                $query->avatar = $request->avatar;
                $query->save();
            }
            
        }else{
                $query = new ReviewCus();
                $query->name = ($request->name);
                $query->position = ($request->position);
                $query->content = ($request->content);
                $query->status = $request->status;
                $query->avatar = $request->avatar;
                $query->save();
            
        }
        return $query;
    }
}
