<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('mentor_id');
            $table->bigInteger('course_id')->nullable();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->dateTime('deadline');
            $table->enum('status', ['Ongoing', 'Unpublished', 'OnHold', 'Finished'])->default('Unpublished');
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
        Schema::dropIfExists('assignments');
    }
}
