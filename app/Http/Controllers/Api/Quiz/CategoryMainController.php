<?php

namespace App\Http\Controllers\Api\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Quiz\CategoryMain;
use App\models\Quiz\QuizCategory;

class CategoryMainController extends Controller
{
    public function add(Request $request, CategoryMain $category)
    {
        $data = $category->saveCate($request);
        return response()->json([
    		'message' => 'Save Success',
    		'data'=> $data
    	],200);
    }
    public function findCategory($id)
    {
        $data = QuizCategory::where(['cate_id'=>$id])->get();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }
    public function list(Request $request)
    {
        $keyword = $request->keyword;
        if($keyword == ""){
            $data = CategoryMain::orderBy('id','DESC')->get();
        }else{
            $data = CategoryMain::where('name', 'LIKE', '%'.$keyword.'%')->orderBy('id','DESC')->get();
        }
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function edit($id)
    {
        $data = CategoryMain::where(['id'=>$id])->first();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }
    public function delete( $id)
    {
        $query = CategoryMain::find($id);
        $query->delete();
        return response()->json(['message'=>'Delete Success']);
    }
}
