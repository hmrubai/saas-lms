<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterQuizWrittenAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_quiz_written_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_quiz_result_id');
            $table->bigInteger('chapter_quiz_id');
            $table->bigInteger('user_id');
            $table->string('attachment_url');
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
        Schema::dropIfExists('chapter_quiz_written_attachments');
    }
}
