<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsReadToTrainerQuoteInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainer_quote_invoices', function (Blueprint $table) {
            $table->tinyInteger('is_read')->after('invoice_date')->default(config('constants.PAYMENT.READ_NOTIFICATION'))->comment('1 => Read, 0 => Unread');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainer_quote_invoices', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
}
