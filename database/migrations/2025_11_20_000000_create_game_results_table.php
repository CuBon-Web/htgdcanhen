<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameResultsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_export_id')->nullable()->index();
            $table->string('game_id')->index();
            $table->unsignedInteger('student_id')->nullable()->index();
            $table->string('student_name')->nullable();
            $table->string('student_email')->nullable();
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('status')->nullable();
            $table->json('earned_rewards')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('game_export_id')
                ->references('id')
                ->on('game_exports')
                ->onDelete('set null');

            $table->foreign('student_id')
                ->references('id')
                ->on('customer')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_results');
    }
};

