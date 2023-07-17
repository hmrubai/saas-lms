<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsFieldInCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('link')->nullable()->after('name');
            $table->boolean('is_authentication_needed')->default(0)->after('link');
            $table->boolean('has_submenu')->default(1)->after('is_authentication_needed');
            $table->boolean('is_course')->default(1)->after('has_submenu');
            $table->boolean('is_content')->default(0)->after('is_course');
            $table->string('icon')->nullable()->after('is_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('link');
            $table->dropColumn('is_authentication_needed');
            $table->dropColumn('has_submenu');
            $table->dropColumn('is_course');
            $table->dropColumn('is_content');
            $table->dropColumn('icon');
        });
    }
}
