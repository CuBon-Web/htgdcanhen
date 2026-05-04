<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\models\Document;

class BillDocument extends Model
{
    protected $table = "bill_document";
    public function customer()
    {
        return $this->hasOne(Customer::class,'id','customer_id');
    }
    public function document()
    {
        return $this->hasOne(Document::class,'id','document_id');
    }
    public function checkBillStatus($customer_id, $document_id){
        $bill = BillDocument::where(['customer_id'=>$customer_id,'document_id'=>$document_id])->first();
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
