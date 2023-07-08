<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicConsumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_consumes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payment_id');
            $table->bigInteger('user_id');
            $table->bigInteger('school_id')->nullable();
            $table->bigInteger('package_id');
            $table->bigInteger('package_type_id');
            $table->integer('balance')->default(0);
            $table->integer('consumme')->default(0);
            $table->dateTime('expiry_date');
            $table->boolean('is_fully_consumed')->default(0);
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
        Schema::dropIfExists('topic_consumes');
    }
}
