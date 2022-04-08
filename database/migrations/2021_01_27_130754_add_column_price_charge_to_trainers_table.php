<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPriceChargeToTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->double('training_price')->after('reset_token_expired_time')->nullable();
            $table->double('job_support_price')->after('reset_token_expired_time')->nullable();
            $table->double('interview_support_price')->after('reset_token_expired_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropColumn('training_price');
            $table->dropColumn('job_support_price');
            $table->dropColumn('interview_support_price');
        });
    }
}
