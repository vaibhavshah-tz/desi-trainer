<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('timezone_id')->constrained('timezones');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('api_token')->unique()->nullable()->default(null);
            $table->tinyInteger('gender')->comment('1 => Male, 2 => Female');
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->string('username')->nullable();
            $table->string('avatar')->nullable();
            $table->tinyInteger('user_type')->comment('1 => Individual, 2 => Employer');
            $table->string('country_code');
            $table->string('phone_number');
            $table->string('city');
            $table->string('zipcode');
            $table->integer('otp')->nullable();
            $table->timestamp('otp_expired_time')->nullable();
            $table->tinyInteger('is_otp_verified')->default(0)->comment('1 => Verified, 0 => Not verified');
            $table->string('reset_password_token')->nullable();
            $table->timestamp('reset_token_expired_time')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 => Active, 0 => Inactive');
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
        Schema::dropIfExists('customers');
    }
}
