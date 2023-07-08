<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payment_id');
            $table->bigInteger('user_id');
            $table->bigInteger('school_id')->nullable();
            $table->bigInteger('package_id');
            $table->bigInteger('package_type_id');
            $table->float('unit_price')->default(0.00);
            $table->float('quantity')->default(0.00);
            $table->float('total')->default(0.00);
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
        Schema::dropIfExists('payment_details');
    }
}
