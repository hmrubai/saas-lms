<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterQuizResultAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_quiz_result_answers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_quiz_result_id');
            $table->bigInteger('question_id');
            $table->boolean('answer1')->default(0);
            $table->boolean('answer2')->default(0);
            $table->boolean('answer3')->default(0);
            $table->boolean('answer4')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chapter_quiz_result_answers');
    }
}
