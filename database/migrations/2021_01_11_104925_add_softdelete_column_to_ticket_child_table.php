<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftdeleteColumnToTicketChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_quotes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('trainer_quotes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('customer_quote_installments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('trainer_quote_invoices', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_quotes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('trainer_quotes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('customer_quote_installments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('trainer_quote_invoices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
