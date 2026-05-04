<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceColumnToBillCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_course', function (Blueprint $table) {
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('quantity')->default(0);
        });
        Schema::table('bill_document', function (Blueprint $table) {
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('quantity')->default(0);
        });
        Schema::table('bill_dethi', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_course', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('quantity');
        });
        Schema::table('bill_document', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('quantity');
        });
        Schema::table('bill_dethi', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
}
