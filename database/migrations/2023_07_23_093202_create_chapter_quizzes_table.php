<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->text('description')->nullable();
            $table->string('quiz_code');
            $table->bigInteger('class_level_id');
            $table->bigInteger('subject_id');
            $table->bigInteger('chapter_id');
            $table->float('duration')->default(0.00);
            $table->float('positive_mark')->default(1.00);
            $table->float('negative_mark')->default(0.00);
            $table->float('total_mark')->default(0.00);
            $table->boolean('number_of_question')->default(1);
            $table->boolean('is_free')->default(1);
            $table->integer('sequence')->default(0);
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
        Schema::dropIfExists('chapter_quizzes');
    }
}
