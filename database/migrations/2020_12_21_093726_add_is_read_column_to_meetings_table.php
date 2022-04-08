<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReadColumnToMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->tinyInteger('trainer_is_read')->default(config('constants.UNREAD_NOTIFICATION'))->comment('1 => Read, 0 => Unread')->after('create_meeting_with');
            $table->tinyInteger('customer_is_read')->default(config('constants.UNREAD_NOTIFICATION'))->comment('1 => Read, 0 => Unread')->after('create_meeting_with');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['trainer_is_read', 'customer_is_read']);
        });
    }
}
