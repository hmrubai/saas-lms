<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterQuizSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_quiz_subjects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_quiz_id');
            $table->bigInteger('quiz_core_subject_id');
            $table->float('no_of_question')->default(0.00);
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
        Schema::dropIfExists('chapter_quiz_subjects');
    }
}
