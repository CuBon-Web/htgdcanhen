<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\product\Product;
use App\Customer;

class BillCourse extends Model
{
    protected $table = "bill_course";
    public function customer()
    {
        return $this->hasOne(Customer::class,'id','customer_id');
    }
    public function product()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
    public function checkBillStatus($customer_id, $course_id){
        $bill = BillCourse::where(['customer_id'=>$customer_id,'product_id'=>$course_id])->first();
        if($bill){
            if($bill->status == 1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
