<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_email_unique');
        });
        // Schema::table('trainers', function (Blueprint $table) {
        //     $table->dropUnique('trainers_email_unique');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email')->change();
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->unique('email')->change();
        });
        // Schema::table('trainers', function (Blueprint $table) {
        //     $table->unique('email')->change();
        // });
    }
}
