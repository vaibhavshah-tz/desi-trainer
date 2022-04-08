<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReadCountColumnForBottomNavigation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->tinyInteger('is_read')->after('status')->default(config('constants.UNREAD'))->comment('0 => Unread, 1 => Read');
        });
        Schema::table('proposal_trainers', function (Blueprint $table) {
            $table->tinyInteger('is_read')->after('denied_reason')->default(config('constants.UNREAD'))->comment('0 => Unread, 1 => Read');
        });
        Schema::table('interested_ticket_trainer', function (Blueprint $table) {
            $table->tinyInteger('is_read')->after('trainer_id')->default(config('constants.UNREAD'))->comment('0 => Unread, 1 => Read');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
        Schema::table('proposal_trainers', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
        Schema::table('interested_ticket_trainer', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
}
