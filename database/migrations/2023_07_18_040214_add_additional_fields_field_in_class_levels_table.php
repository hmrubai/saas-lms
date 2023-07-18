<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsFieldInClassLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('class_levels', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('is_free');
            $table->string('color_code')->nullable()->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_levels', function (Blueprint $table) {
            $table->dropColumn('icon');
            $table->dropColumn('color_code');
        });
    }
}
