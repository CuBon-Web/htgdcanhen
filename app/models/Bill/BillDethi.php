<?php

namespace App\models\Bill;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\models\dethi\Dethi;

class BillDethi extends Model
{
    protected $table = "bill_dethi"; 
    public function customer()
    {
        return $this->hasOne(Customer::class,'id','student_id');
    }
    public function dethi()
    {
        return $this->hasOne(Dethi::class,'id','dethi_id');
    }
    public function checkBillDethiStatus($customer_id, $course_id){
        $bill = BillDethi::where(['student_id'=>$customer_id,'dethi_id'=>$course_id])->first();
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
