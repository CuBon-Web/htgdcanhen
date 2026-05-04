<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeClassCodeToJson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Chuyển đổi dữ liệu hiện có từ string sang JSON array
        $customers = DB::table('customer')->whereNotNull('class_code')->get();
        
        foreach ($customers as $customer) {
            // Chuyển class_code thành mảng JSON
            $classCodes = [$customer->class_code];
            DB::table('customer')
                ->where('id', $customer->id)
                ->update(['class_code' => json_encode($classCodes)]);
        }
        
        // Thay đổi kiểu dữ liệu của cột class_code
        Schema::table('customer', function (Blueprint $table) {
            $table->json('class_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Chuyển đổi dữ liệu từ JSON về string (lấy phần tử đầu tiên)
        $customers = DB::table('customer')->whereNotNull('class_code')->get();
        
        foreach ($customers as $customer) {
            $classCodes = json_decode($customer->class_code, true);
            if (is_array($classCodes) && !empty($classCodes)) {
                DB::table('customer')
                    ->where('id', $customer->id)
                    ->update(['class_code' => $classCodes[0]]);
            } else {
                DB::table('customer')
                    ->where('id', $customer->id)
                    ->update(['class_code' => null]);
            }
        }
        
        // Thay đổi lại kiểu dữ liệu về string
        Schema::table('customer', function (Blueprint $table) {
            $table->string('class_code', 20)->nullable()->change();
        });
    }
}

