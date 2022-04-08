<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainerQuoteInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainer_quote_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_quote_id')->constrained('trainer_quotes');
            $table->string('name')->nullable();
            $table->double('amount');
            $table->string('currency', 20)->nullable();
            $table->string('file')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->tinyInteger('payment_status')->default(config('constants.PAYMENT.DUE'))->comment('1 => Paid, 2 => Due');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainer_quote_invoices');
    }
}
