<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_scripts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('assignment_id');
            $table->bigInteger('student_id');
            $table->bigInteger('chapter_script_id');
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
        Schema::dropIfExists('assignment_scripts');
    }
}
