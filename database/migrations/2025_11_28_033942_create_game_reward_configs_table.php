<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameRewardConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_reward_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('dethi')->onDelete('cascade'); // Game (dethi với type='game')
            $table->foreignId('reward_id')->constrained('game_rewards')->onDelete('cascade'); // Quà tặng
            $table->decimal('min_percentage', 5, 2); // % điểm tối thiểu (ví dụ: 70.00)
            $table->decimal('max_percentage', 5, 2)->nullable(); // % điểm tối đa (null = không giới hạn)
            $table->integer('quantity')->default(1); // Số lượng quà tặng
            $table->integer('priority')->default(0); // Độ ưu tiên (cao hơn = ưu tiên hơn)
            $table->timestamps();
            
            // Index để tìm kiếm nhanh
            $table->index(['game_id', 'min_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_reward_configs');
    }
}
