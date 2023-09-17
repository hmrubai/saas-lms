<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositiveNegativeCountFieldInChapterQuizResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapter_quiz_results', function (Blueprint $table) {
            $table->float('positive_count')->after('mark')->default(0.00);
            $table->float('negetive_count')->after('positive_count')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapter_quiz_results', function (Blueprint $table) {
            $table->dropColumn('positive_count');
            $table->dropColumn('negetive_count');
        });
    }
}
