<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('school_id')->nullable();;
            $table->bigInteger('package_id');
            $table->boolean('is_promo_applied')->default(0);
            $table->bigInteger('promo_id');
            $table->float('payable_amount')->default(0.00);
            $table->float('paid_amount')->default(0.00);
            $table->float('discount_amount')->default(0.00);
            $table->string('currency');
            $table->string('transaction_id');
            $table->string('payment_type');
            $table->string('payment_method');
            $table->enum('status', ['Pending', 'Completed', 'Failed', 'Cancelled'])->default('Pending');
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
        Schema::dropIfExists('payments');
    }
}
