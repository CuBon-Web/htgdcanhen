<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\models\product\Category;
use App\models\product\Product;
use App\models\blog\Blog;
use Session;
use App\models\website\Partner;
use App\models\blog\BlogCategory;
use App\models\ReviewCus;
use App\models\PageContent;
use App\models\Project;
use App\models\website\Founder;
use App\models\BannerAds;
use App\models\Solution;
use App\models\website\AlbumAffter;
use App\models\Promotion;
use App\models\dethi\Dethi;
use App\models\Document;
use App\models\GameExport;


class HomeController extends Controller
{
    public function home()
    {
        $data['hotnews'] = Blog::where([
            ['status','=',1]
        ])->orderBy('id','DESC')->limit(3)->get(['id','title','slug','created_at','image','description']);
        $data['bannerAds'] = BannerAds::where('status',1)->get();
        $data['khoahoc'] = Product::with('customer')->where('status',1)->orderBy('id','DESC')->get();
        // $data['founder'] = Founder::where(['status'=>1])->get();
        $data['gioithieu'] = PageContent::where(['slug'=>'gioi-thieu','language'=>'vi'])->first(['id','title','content','image']);
        $data['partner'] = Partner::where(['status'=>1])->get();
        $data['thanhtich'] = AlbumAffter::where('type',0)->orderBy('id','DESC')->limit(6)->get();
        $data['khacbiet'] = Promotion::orderBy('id','DESC')->get();
        $data['reviewcus'] = ReviewCus::where(['status'=>1])->orderBy('id','DESC')->limit(6)->get();
        $data['founder'] = Solution::where(['status'=>1])->orderBy('id','DESC')->get();
        return view('home',$data);
    }
}
