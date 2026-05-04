<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\product\Product;
use Cart, Auth, Redirect, DB;
use App\models\Bill\BillDetail;
use App\models\Bill\Bill;
use App\models\Bill\BillDethi;
use App\models\BillCourse;
use App\models\BillDocument;
use App\models\dethi\Dethi;
use App\models\Document;

class CartController extends Controller
{
    public function checkout()
    {
        $data['cart'] = session()->get('cart', []);
        $data['profile'] = Auth::guard('customer')->user();
        $total = 0;
        foreach($data['cart'] as $item){
            $total += $item['price'] * $item['quantity'];
        }
        $data['total'] = $total;
        return view('cart.checkout', $data);
    }
    public function postBill(Request $request)
    {
        if(!Auth::guard('customer')->user() != null){
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt hàng');
        }
        $profile = Auth::guard('customer')->user();
        $cart = session()->get('cart', []);
        $code_bill = rand();
        $total = 0;
        DB::beginTransaction();
        try {
            foreach ($cart as $key => $item) {
                if($item['type'] == 'product'){
                    $product = Product::find($item['idpro']);
                    $existBill = BillCourse::where([
                        "customer_id" => $profile->id,
                        "product_id" => $product->id,
                    ])->first();
                    if(!$existBill){
                        $billdetail = new BillCourse();
                        $billdetail->bill_id = $code_bill;
                        $billdetail->product_id = $product->id;
                        $billdetail->price = $item['price'];
                        $billdetail->quantity = $item['quantity'];
                        $billdetail->customer_id = $profile->id;
                        $billdetail->status = 0;
                        $billdetail->save();

                        $total += $item['price'] * $item['quantity'];
                    }
                }
                if($item['type'] == 'document'){
                    $document = Document::find($item['idpro']);
                    $existBill = BillDocument::where([
                        "customer_id" => $profile->id,
                        "document_id" => $document->id,
                    ])->first();
                    if(!$existBill){
                        $billdetail = new BillDocument();
                        $billdetail->bill_id = $code_bill;
                        $billdetail->document_id = $document->id;
                        $billdetail->price = $item['price'];
                        $billdetail->quantity = $item['quantity'];
                        $billdetail->customer_id = $profile->id;
                        $billdetail->status = 0;
                        $billdetail->save();

                        $total += $item['price'] * $item['quantity'];
                    }
                }
                if($item['type'] == 'dethi'){
                    $dethi = Dethi::find($item['idpro']);
                    $existBill = BillDethi::where([
                        "student_id" => $profile->id,
                        "dethi_id" => $dethi->id,
                    ])->first();
                    if(!$existBill){
                        $billdetail = new BillDethi();
                        $billdetail->bill_id = $code_bill;
                        $billdetail->dethi_id = $dethi->id;
                        $billdetail->price = $item['price'];
                        $billdetail->quantity = $item['quantity'];
                        $billdetail->student_id = $profile->id;
                        $billdetail->status = 0;
                        $billdetail->save();

                        $total += $item['price'] * $item['quantity'];
                    }
                }
            }
            $bill = $code_bill;
            DB::commit();
            $request->session()->forget('cart');
            // return Redirect::to('/')->with('success', 'Đặt hàng thành công');
            return view('cart.checkout', compact('total', 'bill'));
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
            return back()->with('error', 'Đặt hàng thất bại');
        }
    }
    public function listCart()
    {
        $data['cart'] = session()->get('cart', []);
        return view('cart.list', $data);
    }
    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $cart[$request->product_id]['quantity'] + $request->quantity;
        } else {
            $cart[$request->product_id] = [
                "idpro" => $request->product_id,
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $product->price,
                "discount" => $product->discount,
                "image" => json_decode($product->images)[0]
            ];
        }

        session()->put('cart', $cart);
        return response()->json($cart);
    }
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            return response()->json($cart);
        }
    }
    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return response()->json($cart);
        }
    }
    public function cartCount()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        foreach($cart as $item){
            $count += $item['quantity'];
        }
        return response()->json(['count' => $count]);
    }
}
