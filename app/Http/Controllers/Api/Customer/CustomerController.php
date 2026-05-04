<?php

namespace App\Http\Controllers\Api\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Customer;


class CustomerController extends Controller
{
    public function list(Request $request)
    {
       
        $keyword = $request->keyword;
        $malophoc = $request->malophoc;
        $data = Customer::query();
        if($keyword != null){
            $data = $data->where('email', 'LIKE', '%'.$keyword.'%')->orderBy('id','DESC');
        }
        else{
            $data = $data->orderBy('id','DESC');
        }


        $data = $data->get();
       
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function create(Request $request)
    {
        $data = new Customer();
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->note = $request->note;
        $data->status = 0;
        $data->save();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function deleteCustomer($id) {
        $query = Customer::where('id',$id)->first();
        $query->delete();
        return response()->json([
            'message' => 'success',
            'data' => $query
        ], 200);
    }
    public function getEdit($id)
    {
        $data = Customer::find($id);
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function activeCustomer(Request $request)
    {
        $data = Customer::where('email',$request->email)->first();
        // $data->password = bcrypt($request->password);
        $data->status = 0;
        $data->save();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function resetPass(Request $request)
    {
        $data = Customer::where('email',$request->email)->first();
        $data->password = bcrypt($request->password);
        $data->status = 0;
        $data->save();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function changeStatus(Request $request)
    {
        $data = Customer::where('email',$request->email)->first();
        $data->status = 1;
        $data->save();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function postEdit(Request $request)
    {
        $data = Customer::where('id',$request->id)->first();
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->note = $request->note;
        $data->save();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }

}
