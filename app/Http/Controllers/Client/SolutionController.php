<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Solution;

class SolutionController extends Controller
{
    public function detail($slug)
    {
        $data['solution'] = Solution::where('slug',$slug)->first();
        $data['list'] = Solution::where('status',1)->orderBy('id','DESC')->limit(10)->get();
        return view('solution',$data);
    }
    public function list()
    {
        $data['list'] = Solution::where('status',1)->orderBy('id','DESC')->paginate(20);
        return view('solutionlist',$data);
    }
}
