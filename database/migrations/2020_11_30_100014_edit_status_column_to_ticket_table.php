<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EditStatusColumnToTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            DB::statement("ALTER TABLE `tickets` CHANGE `status` `status` TINYINT(4) NOT NULL DEFAULT '1' COMMENT '1 => New, 2 => Pending, 3 => In progress, 4 => Inactive , 5 => Closed , 6 => Cancel'");
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
            DB::statement("ALTER TABLE `tickets` CHANGE `status` `status` TINYINT(4) NOT NULL DEFAULT '1' COMMENT '1 => Pending, 2 => Unassigned, 3 => In progress, 4 => Assigned, 5 => Closed, 6 => Inactive';");
        });
    }
}
