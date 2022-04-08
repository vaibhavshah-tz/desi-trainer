<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('country_id')->index();
            $table->unsignedBigInteger('course_category_id')->index();
            $table->unsignedBigInteger('timezone_id')->index();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->tinyInteger('gender')->comment('1 => Male, 2 => Female');
            $table->string('email', 100);
            $table->string('password');
            $table->string('api_token')->unique()->nullable()->default(null);
            $table->string('username', 100)->nullable();
            $table->string('avatar')->nullable();
            $table->string('certificate')->nullable();
            $table->string('country_code', 20);
            $table->string('phone_number', 50);
            $table->string('city', 100);
            $table->string('zipcode', 20);
            $table->string('skill_title');
            $table->integer('total_experience_year');
            $table->integer('total_experience_month');
            $table->integer('prior_teaching_experience_year');
            $table->integer('prior_teaching_experience_month');
            $table->string('resume')->nullable();            
            $table->integer('otp')->nullable();
            $table->timestamp('otp_expired_time')->nullable();
            $table->tinyInteger('is_otp_verified')->default(0)->comment('1 => verified, 0 => not verified');
            $table->string('reset_password_token')->nullable();
            $table->timestamp('reset_token_expired_time')->nullable();
            $table->tinyInteger('status')->default(2)->comment('0 => Inactive, 1 => Active, 2 => Not verify');
            $table->timestamps();
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainers');
    }
}
