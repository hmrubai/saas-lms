<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseOutlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_outlines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->bigInteger('course_id');
            $table->bigInteger('class_level_id');
            $table->bigInteger('subject_id');
            $table->bigInteger('chapter_id');
            $table->bigInteger('chapter_script_id')->default(0);
            $table->bigInteger('chapter_video_id')->default(0);
            $table->bigInteger('chapter_quiz_id')->default(0);
            $table->boolean('is_free')->default(0);
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
        Schema::dropIfExists('course_outlines');
    }
}
