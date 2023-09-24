<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterQuizWrittenMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_quiz_written_marks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_quiz_result_id');
            $table->bigInteger('chapter_quiz_id');
            $table->bigInteger('user_id');
            $table->integer('question_no')->default(0);
            $table->float('mark')->default(0.00);
            $table->bigInteger('marks_givenby_id')->default(0);
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
        Schema::dropIfExists('chapter_quiz_written_marks');
    }
}
