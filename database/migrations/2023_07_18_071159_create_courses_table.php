<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('gp_product_id')->nullable();
            $table->text('description')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('icon')->nullable();
            $table->integer('number_of_enrolled')->default(0);
            $table->float('regular_price')->default(0.00);
            $table->float('sale_price')->default(0.00);
            $table->float('discount_percentage')->default(0.00);
            $table->float('rating')->default(0.00);
            $table->boolean('is_free')->default(1);
            $table->integer('sequence')->default(0);
            $table->dateTime('appeared_from')->nullable();
            $table->dateTime('appeared_to')->nullable();
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
        Schema::dropIfExists('courses');
    }
}
