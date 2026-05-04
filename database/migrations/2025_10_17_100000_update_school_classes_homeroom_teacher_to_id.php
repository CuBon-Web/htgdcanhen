<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSchoolClassesHomeroomTeacherToId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Kiểm tra và xóa cột cũ nếu tồn tại
        if (Schema::hasColumn('school_classes', 'homeroom_teacher')) {
            Schema::table('school_classes', function (Blueprint $table) {
                $table->dropColumn('homeroom_teacher');
            });
        }
        
        // Thêm cột mới nếu chưa có (dùng unsignedInteger để tương thích với customer.id)
        if (!Schema::hasColumn('school_classes', 'homeroom_teacher_id')) {
            Schema::table('school_classes', function (Blueprint $table) {
                $table->unsignedInteger('homeroom_teacher_id')->nullable()->after('class_code');
                $table->foreign('homeroom_teacher_id')->references('id')->on('customer')->onDelete('set null');
            });
        }
        
        // Đảm bảo class_code là unique (nếu chưa unique)
        \DB::statement('ALTER TABLE school_classes MODIFY COLUMN class_code VARCHAR(20) UNIQUE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropForeign(['homeroom_teacher_id']);
            $table->dropColumn('homeroom_teacher_id');
            $table->string('homeroom_teacher')->nullable();
        });
    }
}

