<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_subjects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('content_id');
            $table->bigInteger('class_level_id');
            $table->bigInteger('subject_id');
            $table->float('price')->default(0.00);
            $table->string('icon')->nullable();
            $table->string('color_code')->nullable();
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
        Schema::dropIfExists('content_subjects');
    }
}
