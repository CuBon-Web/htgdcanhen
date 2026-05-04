<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\models\website\AlbumAffter;

class AlbumAffterController extends Controller
{
    public function createOrUpdateAlbumAffter(Request $request)
    {
		
    	if($request->data){
    		AlbumAffter::where('type',0)->delete();
	    	foreach ($request->data as $key => $value) {
	    		AlbumAffter::updateOrCreate(
				    ['image' => $value['image'],
					'avatar' => $value['avatar'],
				     'status' =>$value['status'],
				     'name' =>$value['name'],
					 'type' =>$value['type'],
					 'link' => $value['link']
				 	]
				);
	    	}
    	}
    	return response()->json([
            'messenge' => 'success'
        ],200);
    }
    public function listAlbumAfftero()
    {
    	$data = AlbumAffter::where('type',0)->get();
    	return response()->json([
            'messenge' => 'success',
            'data' => $data
        ],200);
	}
}
