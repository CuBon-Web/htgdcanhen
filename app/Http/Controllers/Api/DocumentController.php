<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Document;

class DocumentController extends Controller
{
    public function create(Request $request, Document $ser)
    {
    	$data = $ser->saveDocument($request);
        return response()->json([
    		'message' => 'Save Success',
    		'data'=> $data
    	],200);
    }
    public function list(Request $request)
    {
    	$keyword = $request->keyword;
        if($keyword == ""){
            $data = Document::with('cate')->orderBy('order_id','ASC')->get(['id','name','created_at','image','cate_id','order_id']);
        }else{
            $data = Document::with('cate')->where('name', 'LIKE', '%'.$keyword.'%')->orderBy('order_id','ASC')->get();
        }
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function delete($id)
    {
        $query = Document::find($id);
        if($query->image){
            $imgArr = json_decode($query->image);
            foreach($imgArr as $i){
                $file= str_replace('http://localhost:8080','',$i);
                $filename = public_path().$file;
                if(file_exists( public_path().$file ) ){
                    \File::delete($filename);
                }
            }
        }
        $query->delete();
        return response()->json(['message'=>'Delete Success'],200);
    }
    public function edit($id)
    {
        $data = Document::where([
            'id'=> $id
        ])
        ->first();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
}
