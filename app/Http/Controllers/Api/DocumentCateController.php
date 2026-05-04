<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\DocumentCate;

class DocumentCateController extends Controller
{
    public function add(Request $request, DocumentCate $category)
    {
        $data = $category->saveCate($request);
        return response()->json([
    		'message' => 'Save Success',
    		'data'=> $data
    	],200);
    }
    public function list(Request $request)
    {
        $keyword = $request->keyword;
        if($keyword == ""){
            $data = DocumentCate::orderBy('order_id','ASC')->get();
        }else{
            $data = DocumentCate::where('name', 'LIKE', '%'.$keyword.'%')->orderBy('order_id','ASC')->get();
        }
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function edit($id)
    {
        $data = DocumentCate::where(['id'=>$id])->first();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }
    public function delete( $id)
    {
        $query = DocumentCate::find($id);
        $query->delete();
        return response()->json(['message'=>'Delete Success']);
    }
}
