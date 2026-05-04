<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDethiClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dethi_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dethi_id')->constrained('dethi')->onDelete('cascade')->comment('ID đề thi');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade')->comment('ID lớp học');
            $table->text('description')->nullable()->comment('Mô tả thêm');
            $table->timestamps();
            
            // Index và unique constraint
            $table->unique(['dethi_id', 'class_id'], 'dethi_class_unique');
            $table->index('class_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dethi_classes');
    }
}
