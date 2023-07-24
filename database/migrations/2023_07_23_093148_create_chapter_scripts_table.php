<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_scripts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->text('description')->nullable();
            $table->string('script_code');
            $table->bigInteger('class_level_id');
            $table->bigInteger('subject_id');
            $table->bigInteger('chapter_id');
            $table->text('raw_url')->nullable();
            $table->string('thumbnail')->nullable();
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
        Schema::dropIfExists('chapter_scripts');
    }
}
