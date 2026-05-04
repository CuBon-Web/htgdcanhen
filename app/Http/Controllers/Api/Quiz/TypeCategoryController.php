<?php

namespace App\Http\Controllers\Api\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Quiz\TypeCategory;

class TypeCategoryController extends Controller
{
    public function add(Request $request, TypeCategory $category)
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
            $data = TypeCategory::with('cate_main','cate')->orderBy('id','DESC')->get();
        }else{
            $data = TypeCategory::with('cate_main','cate')->where('name', 'LIKE', '%'.$keyword.'%')->orderBy('id','DESC')->get();
        }
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function edit($id)
    {
        $data = TypeCategory::where(['id'=>$id])->first();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }
    public function delete( $id)
    {
        $query = TypeCategory::find($id);
        $query->delete();
        return response()->json(['message'=>'Delete Success']);
    }
}
