<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('video_code');
            $table->bigInteger('class_level_id');
            $table->bigInteger('subject_id');
            $table->bigInteger('chapter_id');
            $table->string('author_name')->nullable();
            $table->string('author_details')->nullable();
            $table->text('description')->nullable();
            $table->text('raw_url')->nullable();
            $table->text('s3_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->text('download_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('duration')->nullable();
            $table->float('price')->default(0.00);
            $table->float('rating')->default(0.00);
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
        Schema::dropIfExists('chapter_videos');
    }
}
