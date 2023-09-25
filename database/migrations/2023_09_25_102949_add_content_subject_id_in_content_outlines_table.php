<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentSubjectIdInContentOutlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('content_outlines', function (Blueprint $table) {
            $table->bigInteger('content_subject_id')->nullable()->after('content_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_outlines', function (Blueprint $table) {
            $table->dropColumn('content_subject_id');
        });
    }
}
