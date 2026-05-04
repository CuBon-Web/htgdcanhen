<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\BillDocument;
use App\models\Services;
use App\models\Document;
use App\models\DocumentCate;
use Auth;

class DocumentController extends Controller
{
    public function documentList($danhmuc){
        $data['list'] = Document::where('cate_slug',$danhmuc)->orderBy('id','ASC')->paginate(9);
        $data['cate'] = DocumentCate::where('slug',$danhmuc)->first();
        return view('document.list',$data);

    }
    public function documentDetail($danhmuc,$slug){
        $data['detail_service'] = Document::with(['cate'])->where(['cate_slug'=>$danhmuc, 'slug'=>$slug])->first();
        $data['servicelq'] = Document::where(['cate_slug'=>$danhmuc])->get();
        return view('document.detail',$data);

    }
    public function dangkytailieu($documentid,$documentslug) {

        $document = Document::where(['id'=>$documentid,'slug'=>$documentslug])->first();
        if(Auth::guard('customer')->user() != null){
            $profile = Auth::guard('customer')->user();
        }else{
            $profile = "";
        }
        $existBill = BillDocument::where(['customer_id'=>$profile->id,'document_id'=>$document->id])->first();
        if($existBill){
            return view("document.dangkymuathanhcong",['document' => $document,'bill' => $existBill])->with('error', 'Bạn đã đăng ký tài liệu này');
        }else {
            $course = new BillDocument;
            $course->bill_id = rand();
            $course->customer_id = $profile->id;
            $course->document_id = $documentid;
            $course->status = 0;
            $course->save();
            if($course){
                return view("document.dangkymuathanhcong",['document' => $document,'bill' => $course])->with("success", "Bạn đã đăng ký tài liệu này");
            }
        }

    }

    public function themvaoGioHangTailieu(Request $request)
    {
        $document = Document::where([
            "id" => $request->document_id,
            "slug" => $request->slug,
        ])->first();

        $cart = session()->get('cart', []);

        if(isset($cart[$request->document_id]) && $cart[$request->document_id]['type'] == 'document') {
            $cart[$request->document_id]['quantity'] = $cart[$request->document_id]['quantity'] + 1;
        } else {
            $cart[$request->document_id] = [
                "idpro" => $request->document_id,
                "name" => $document->name,
                "quantity" => 1,
                "price" => $document->price,
                "discount" => 0,
                "image" => $document->image,
                "type" => 'document'
            ];
        }

        session()->put('cart', $cart);

        $countCart = 0;
        foreach($cart as $item){
            $countCart += $item['quantity'];
        }
        return response()->json(['data' => $cart, 'message' => 'Thêm vào giỏ hàng thành công', 'success' => true, 'count' => $countCart]);
    }
}
