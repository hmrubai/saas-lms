<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_quiz_id');
            $table->bigInteger('class_level_id');
            $table->bigInteger('subject_id');
            $table->bigInteger('chapter_id');
            $table->string('question_text')->nullable();
            $table->string('question_text_bn')->nullable();
            $table->string('question_image')->nullable();
            $table->text('option1')->nullable();
            $table->text('option2')->nullable();
            $table->text('option3')->nullable();
            $table->text('option4')->nullable();
            $table->text('option1_image')->nullable();
            $table->text('option2_image')->nullable();
            $table->text('option3_image')->nullable();
            $table->text('option4_image')->nullable();
            $table->boolean('answer1')->default(0);
            $table->boolean('answer2')->default(0);
            $table->boolean('answer3')->default(0);
            $table->boolean('answer4')->default(0);
            $table->string('explanation_text');
            $table->string('explanation_image');
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('chapter_quiz_questions');
    }
}
