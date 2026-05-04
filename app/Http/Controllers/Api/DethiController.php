<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\dethi\Dethi;
use Illuminate\Support\Facades\Storage;
class DethiController extends Controller
{
    public function addDethi(Request $request){
        $data = new Dethi();
        $data->name = $request->name;
        $data->save();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function listDethi(Request $request){
        $keyword = $request->keyword;
        if($keyword == ""){
            $data = Dethi::with(['customer','sessions','bill_dethi'])->where('type','dethi')->orderBy('status','ASC')->get();
        }else{
            $data = Dethi::with(['customer','sessions','bill_dethi'])->where('name', 'LIKE', '%'.$keyword.'%')->where('type','dethi')->orderBy('status','ASC')->get();
        }
        return response()->json([
    		'message' => 'Success',
    		'data'=> $data
    	],200);
    }
    public function changeStatusDethi(Request $request){
        $arrayId = $request->data;
        $status = $request->status;
        foreach($arrayId as $id){
            $data = Dethi::find($id);
            $data->status = $status;
            $data->save();
        }
        return response()->json([
            'message' => 'Success',
            'data'=> $data
        ],200);
    }
    public function deleteDethiArrayId(Request $request){
        $arrayId = $request->id;
        foreach($arrayId as $id){
            \DB::beginTransaction();
        try {
            $dethi = Dethi::with(['parts.questions.answers', 'sessions.answers'])->findOrFail($id);
            // Xóa file audio của từng câu hỏi (nếu có)
            foreach ($dethi->parts as $part) {
                foreach ($part->questions as $question) {
                    // Xóa file audio nếu có
                    if ($question->audio) {
                        Storage::disk('public')->delete($question->audio);
                    }
                }
            }
            // Xóa file ảnh của các bài làm (exam_answers)
            foreach ($dethi->sessions as $session) {
                foreach ($session->answers as $answer) {
                    if ($answer->answer_image) {
                        Storage::disk('public')->delete($answer->answer_image);
                    }
                }
            }   
            // Xóa các session và answer
            foreach ($dethi->sessions as $session) {
                foreach ($session->answers as $answer) {
                    $answer->delete();
                }
                $session->delete();
            }
            // Xóa các câu hỏi, đáp án, part
            foreach ($dethi->parts as $part) {
                foreach ($part->questions as $question) {
                    foreach ($question->answers as $ans) {
                        $ans->delete();
                    }
                    $question->delete();
                }
                $part->delete();
            }
            // Xóa đề thi
            $dethi->delete();
            \DB::commit();
            return response()->json([
                'message' => 'Success'
            ],200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Error'
            ],500);
        }
        }
    }
}
