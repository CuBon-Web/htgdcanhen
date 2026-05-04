<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\models\website\AlbumAffter;

class SocicalFeedbackController extends Controller
{
    public function createOrUpdateSocicalFeedback(Request $request)
    {

    	if($request->data){
    		AlbumAffter::where('type',1)->delete();

	    	foreach ($request->data as $key => $value) {
	    		AlbumAffter::updateOrCreate(
				    ['image' => $value['image'],
					'avatar' => $value['avatar'],
				     'status' =>$value['status'],
                     'date' =>$value['date'],
				     'name' =>$value['name'],
					 'type' =>1,
					 'link' => $value['link']
				 	]
				);
	    	}
    	}
    	return response()->json([
            'messenge' => 'success'
        ],200);
    }
    public function listSocicalFeedback()
    {
    	$data = AlbumAffter::where('type',1)->get();
    	return response()->json([
            'messenge' => 'success',
            'data' => $data
        ],200);
	}
}
