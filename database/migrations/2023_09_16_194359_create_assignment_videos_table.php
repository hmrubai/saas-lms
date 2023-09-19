<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_videos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('assignment_id');
            $table->bigInteger('student_id');
            $table->bigInteger('chapter_video_id');
            $table->boolean('has_completed')->default(0);
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
        Schema::dropIfExists('assignment_videos');
    }
}
