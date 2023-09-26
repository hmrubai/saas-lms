<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuizTypeIdInChapterQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapter_quizzes', function (Blueprint $table) {
            $table->string('quiz_type_id')->default(null)->after('chapter_id');
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
            $table->dropColumn('quiz_type_id');
        });
    }
}
