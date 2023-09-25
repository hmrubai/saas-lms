<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitInQuizParticipationCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_participation_counts', function (Blueprint $table) {
            $table->integer('limit')->default(1)->after('number_of_participation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_participation_counts', function (Blueprint $table) {
            $table->dropColumn('limit');
        });
    }
}
