<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParticipationLimitInCourseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_types', function (Blueprint $table) {
            $table->integer('participation_limit')->default(0)->after('name_bn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_types', function (Blueprint $table) {
            $table->dropColumn('participation_limit');
        });
    }
}
