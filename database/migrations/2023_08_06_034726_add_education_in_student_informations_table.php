<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEducationInStudentInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_informations', function (Blueprint $table) {
            $table->string('education')->nullable()->after('organization_slug');
            $table->string('institute')->nullable()->after('education');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_informations', function (Blueprint $table) {
            $table->dropColumn('education');
            $table->dropColumn('institute');
        });
    }
}
