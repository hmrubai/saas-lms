<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddS3UrlInChapterScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapter_scripts', function (Blueprint $table) {
            $table->string('s3_url')->nullable()->after('raw_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapter_scripts', function (Blueprint $table) {
            $table->dropColumn('s3_url');
        });
    }
}
