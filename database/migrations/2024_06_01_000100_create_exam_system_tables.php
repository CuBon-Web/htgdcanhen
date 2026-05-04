<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamSystemTables extends Migration
{
    public function up()
    {
        // Bảng exams
        Schema::create('dethi', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('grade');
            $table->integer('subject');
            $table->integer('time');
            $table->text('description')->nullable();
            $table->foreignId('created_by');
            $table->json('part_scores')->nullable();
            $table->json('true_false_score_percent')->nullable();
            $table->integer('price');
            $table->string('pricing_type');
            $table->integer('status');
            $table->timestamps();
        });

        // Bảng exam_parts
        Schema::create('dethi_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dethi_id')->constrained('dethi')->onDelete('cascade');
            $table->string('part');
            $table->string('part_title')->nullable();
            $table->timestamps();
        });

        // Bảng questions
        Schema::create('dethi_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('de_thi_id');
            $table->foreignId('dethi_part_id')->constrained('dethi_parts')->onDelete('cascade');
            $table->integer('question_no');
            $table->text('content');
            $table->string('question_type');
            $table->float('score')->nullable();
            $table->string('audio')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        // Bảng answers
        Schema::create('dethi_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('de_thi_id');
            $table->foreignId('dethi_question_id');
            $table->string('label');
            $table->text('content');
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->nullable();
            $table->timestamps();
        });

        // Bảng exam_sessions
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dethi_id')->constrained('dethi')->onDelete('cascade');
            $table->foreignId('student_id');
            $table->foreignId('teacher_id');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('status')->default(0); // 0: đang làm, 1: đã hoàn thành
            $table->float('total_score')->nullable();
            $table->float('max_score')->nullable();
            $table->float('percentage')->nullable();
            $table->integer('correct_answers')->nullable();
            $table->integer('total_questions')->nullable();
            $table->json('part_results')->nullable();
            $table->timestamps();
        });

        // Bảng exam_answers
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('dethi_questions')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->string('answer_image')->nullable();
            $table->string('answer_choice')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->float('score')->nullable();
            $table->foreignId('graded_by')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->text('teacher_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('dethi_answers');
        Schema::dropIfExists('dethi_questions');
        Schema::dropIfExists('dethi_parts');
        Schema::dropIfExists('dethi');
    }
}; 