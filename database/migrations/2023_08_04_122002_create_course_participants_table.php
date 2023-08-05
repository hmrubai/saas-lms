<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_participants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_id');
            $table->bigInteger('user_id');
            $table->float('item_price')->default(0.00);
            $table->float('paid_amount')->default(0.00);
            $table->float('discount')->default(0.00);
            $table->enum('item_type', ['Course', 'Content', 'Others'])->default('Course');
            $table->boolean('is_trial_taken')->default(1);
            $table->dateTime('trial_expiry_date')->nullable();
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
        Schema::dropIfExists('course_participants');
    }
}
