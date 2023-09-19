<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_assignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('assignment_id');
            $table->bigInteger('student_id');
            $table->integer('given_video')->default(0);
            $table->integer('completed_video')->default(0);
            $table->integer('given_quiz')->default(0);
            $table->integer('completed_quiz')->default(0);
            $table->integer('given_script')->default(0);
            $table->integer('completed_script')->default(0);
            $table->integer('total_progress')->default(0);
            $table->integer('total_progress_parcentage')->default(0.00);
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
        Schema::dropIfExists('student_assignments');
    }
}
