<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamFoldersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('owner_id');
            $table->foreignId('parent_id')->nullable()->constrained('exam_folders')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['parent_id', 'name']);

            $table->foreign('owner_id')
                ->references('id')
                ->on('customer')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_folders');
    }
};

