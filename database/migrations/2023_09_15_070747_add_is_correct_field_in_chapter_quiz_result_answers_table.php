<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCorrectFieldInChapterQuizResultAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapter_quiz_result_answers', function (Blueprint $table) {
            $table->boolean('is_correct')->default(0)->after('answer4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapter_quiz_result_answers', function (Blueprint $table) {
            $table->dropColumn('is_correct');
        });
    }
}
