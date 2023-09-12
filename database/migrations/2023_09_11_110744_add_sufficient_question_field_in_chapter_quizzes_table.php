<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSufficientQuestionFieldInChapterQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapter_quizzes', function (Blueprint $table) {
            $table->boolean('sufficient_question')->default(0)->after('number_of_question');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapter_quizzes', function (Blueprint $table) {
            $table->dropColumn('sufficient_question');
        });
    }
}
