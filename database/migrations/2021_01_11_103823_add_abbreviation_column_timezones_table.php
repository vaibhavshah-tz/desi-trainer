<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbbreviationColumnTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timezones', function (Blueprint $table) {
            $table->string('country_code')->nullable()->after('label');
            $table->string('latitude')->nullable()->after('label');
            $table->string('longitude')->nullable()->after('label');
            $table->string('abbreviation')->nullable()->after('label');
            $table->string('offset_second')->nullable()->after('offset');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timezones', function (Blueprint $table) {
            $table->dropColumn('abbreviation');
            $table->dropColumn('country_code');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('offset_second');
        });
    }
}
