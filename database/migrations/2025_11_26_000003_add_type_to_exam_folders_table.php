<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToExamFoldersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exam_folders', function (Blueprint $table) {
            $table->string('type', 32)->default('exam')->after('parent_id');
        });

        Schema::table('exam_folders', function (Blueprint $table) {
            if (Schema::hasColumn('exam_folders', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropUnique('exam_folders_parent_id_name_unique');
            }
            $table->unique(['parent_id', 'name', 'type'], 'exam_folders_parent_name_type_unique');
            $table->index(['owner_id', 'type'], 'exam_folders_owner_type_index');
            $table->foreign('parent_id')->references('id')->on('exam_folders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_folders', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropUnique('exam_folders_parent_name_type_unique');
            $table->dropIndex('exam_folders_owner_type_index');
            $table->dropColumn('type');
            $table->unique(['parent_id', 'name'], 'exam_folders_parent_id_name_unique');
            $table->foreign('parent_id')->references('id')->on('exam_folders')->nullOnDelete();
        });
    }
};

