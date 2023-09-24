<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterQuizSubjectWiseResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_quiz_subject_wise_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_quiz_result_id');
            $table->bigInteger('chapter_quiz_id');
            $table->bigInteger('user_id');
            $table->bigInteger('quiz_core_subject_id');
            $table->integer('positive_count')->default(0);
            $table->integer('negetive_count')->default(0);
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
        Schema::dropIfExists('chapter_quiz_subject_wise_results');
    }
}
