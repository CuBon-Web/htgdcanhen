<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name')->unique()->comment('Tên lớp (VD: 10A1, 11B2, 12A3)');
            $table->integer('grade')->comment('Khối lớp (10, 11, 12)');
            $table->string('class_code')->nullable()->comment('Mã lớp (nếu có)');
            $table->integer('total_students')->default(0)->comment('Tổng số học sinh');
            $table->string('homeroom_teacher')->nullable()->comment('Giáo viên chủ nhiệm');
            $table->integer('school_year')->nullable()->comment('Năm học (VD: 2024)');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->timestamps();
            
            // Indexes
            $table->index('grade');
            $table->index('is_active');
            $table->index(['grade', 'class_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_classes');
    }
}
