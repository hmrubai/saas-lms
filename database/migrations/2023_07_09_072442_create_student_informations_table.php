<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_informations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('name');
            $table->string('username');
            $table->string('email')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('student_code');
            $table->string('organization_slug');
            $table->string('device_id')->nullable();
            $table->string('alternative_contact_no')->nullable();
            $table->string('gender')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('bio')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->bigInteger('division_id')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('area_id')->nullable();
            $table->string('nid_no')->nullable();
            $table->string('birth_certificate_no')->nullable();
            $table->string('passport_no')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_foreigner')->default(0);
            $table->float('rating')->default(0.00);
            $table->enum('status', ['Active', 'Pending', 'Suspended', 'On-Hold'])->default('Pending');
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('student_informations');
    }
}
