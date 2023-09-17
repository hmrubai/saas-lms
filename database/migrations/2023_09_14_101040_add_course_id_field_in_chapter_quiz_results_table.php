<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseIdFieldInChapterQuizResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapter_quiz_results', function (Blueprint $table) {
            $table->bigInteger('course_id')->after('chapter_quiz_id')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapter_quiz_results', function (Blueprint $table) {
            $table->dropColumn('course_id');
        });
    }
}
