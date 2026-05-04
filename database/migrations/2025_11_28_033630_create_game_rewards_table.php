<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên quà tặng
            $table->text('description')->nullable(); // Mô tả quà tặng
            $table->string('image')->nullable(); // Hình ảnh quà tặng
            $table->foreignId('created_by')->nullable(); // Người tạo
            $table->integer('status')->default(1); // 1: active, 0: inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_rewards');
    }
}
