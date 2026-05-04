<?php

namespace App\Http\Controllers\Api\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Quiz\QuizCategory;

class QuizCategoryController extends Controller
{
    public function add(Request $request, QuizCategory $category)
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
            $data = QuizCategory::with('cate_main')->orderBy('id','DESC')->get();
        }else{
            $data = QuizCategory::with('cate_main')->where('name', 'LIKE', '%'.$keyword.'%')->orderBy('id','DESC')->get();
        }
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function edit($id)
    {
        $data = QuizCategory::where(['id'=>$id])->first();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }
    public function delete( $id)
    {
        $query = QuizCategory::find($id);
        $query->delete();
        return response()->json(['message'=>'Delete Success']);
    }
}
