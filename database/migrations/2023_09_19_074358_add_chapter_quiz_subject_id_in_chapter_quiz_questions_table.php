<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChapterQuizSubjectIdInChapterQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapter_quiz_questions', function (Blueprint $table) {
            $table->string('chapter_quiz_subject_id')->after('question_set_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapter_quiz_questions', function (Blueprint $table) {
            $table->dropColumn('chapter_quiz_subject_id');
        });
    }
}
