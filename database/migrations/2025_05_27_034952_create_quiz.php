<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuiz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('quiz_results_course', function (Blueprint $table) {
    // $table->id();
    // $table->unsignedBigInteger('user_id');
    // $table->unsignedBigInteger('test_id');
    // $table->integer('score')->nullable();
    // $table->timestamps();
// });

// Schema::create('quiz_result_course_details', function (Blueprint $table) {
//     $table->id();
    // $table->unsignedBigInteger('quiz_result_id');
    // $table->text('question_title');
    // $table->string('user_answer')->nullable();
    // $table->string('right_choice')->nullable();
    // $table->boolean('is_correct')->nullable();
    // $table->text('essay_text')->nullable();
//     $table->timestamps();
// });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('quiz_results_course');
        // Schema::dropIfExists('quiz_result_course_details');
    }
}
