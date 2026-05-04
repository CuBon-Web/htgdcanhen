<?php

namespace App\Http\Controllers\Api\Bill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\BillDocument;

class DocumentController extends Controller
{
    public function draft(Request $request)
    {
        $keyword = $request->keyword;
        $status = $request->status;
        if($keyword == ""){
            $data = BillDocument::with(['document','customer'])->where('status',$status)
            ->orderBy('id','DESC')->get();
        }else{
            $data = BillDocument::with(['document','customer'])->where('bill_id', 'LIKE', '%'.$keyword.'%')->where('status',$status)->orderBy('id','DESC')->get();
        }
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function detail($code)
    {
        $data = BillDocument::with(['document','customer'])->where('bill_id',$code)->first();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function changeStatus(Request $request)
    {
        $data = BillDocument::where('bill_id',$request->bill_id)->first();
        $data->status = $request->status;
        $data->save();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
}
