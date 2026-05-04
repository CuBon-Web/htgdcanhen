<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameExportsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('teacher_id')->nullable()->index();
            $table->string('game_id')->index();
            $table->unsignedInteger('grade_id')->nullable();
            $table->unsignedInteger('subject_id')->nullable();
            $table->string('game_name')->nullable();
            $table->string('game_description')->nullable();
            $table->unsignedInteger('questions_count')->default(0);
            $table->longText('html');
            $table->timestamps();

            $table->foreign('teacher_id')
                ->references('id')
                ->on('customer')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_exports');
    }
};

