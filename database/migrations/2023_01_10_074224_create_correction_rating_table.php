<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correction_ratings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('expert_id');
            $table->float('rating')->default(0.00);
            $table->float('total_rating')->default(0.00);
            $table->float('total_correction')->default(0.00);
            $table->float('rating_avg')->default(0.00);
            $table->timestamps();
        });
    }

    //total_rating
    // total_correction
    // rating_avg

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correction_ratings');
    }
}
