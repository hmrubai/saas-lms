<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->longText('hint');
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('package_type_id');
            $table->bigInteger('catagory_id');
            $table->bigInteger('grade_id')->nullable();
            $table->bigInteger('school_id')->nullable();
            $table->integer('limit')->default(100);
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
        Schema::dropIfExists('topics');
    }
}
