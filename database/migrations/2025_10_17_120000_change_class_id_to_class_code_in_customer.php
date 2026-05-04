<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeClassIdToClassCodeInCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Xóa foreign key constraint nếu có
        Schema::table('customer', function (Blueprint $table) {
            if (Schema::hasColumn('customer', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
        });
        
        // Thêm cột class_code
        Schema::table('customer', function (Blueprint $table) {
            $table->string('class_code', 20)->nullable()->after('phone');
            $table->index('class_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('class_code');
        });
        
        Schema::table('customer', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('set null');
        });
    }
}

