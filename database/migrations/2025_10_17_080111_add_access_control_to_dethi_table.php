<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccessControlToDethiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dethi', function (Blueprint $table) {
            // Cho phép học sinh xem đáp án sau khi làm bài
            $table->boolean('xemdapan')->default(false)->after('status')->comment('Cho phép xem đáp án: 0=Không, 1=Có');
            
            // Loại truy cập: all (tất cả), class (theo lớp), time_limited (theo thời gian)
            $table->enum('access_type', ['all', 'class', 'time_limited'])->default('all')->after('xemdapan')->comment('Loại truy cập');
            
            // Thời gian có thể làm bài (dành cho time_limited)
            $table->timestamp('start_time')->nullable()->after('access_type')->comment('Thời gian bắt đầu cho phép làm bài');
            $table->timestamp('end_time')->nullable()->after('start_time')->comment('Thời gian kết thúc cho phép làm bài');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dethi', function (Blueprint $table) {
            $table->dropColumn(['xemdapan', 'access_type', 'start_time', 'end_time']);
        });
    }
}
