<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerQuoteInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_quote_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_quote_id')->constrained('customer_quotes');
            $table->string('name');
            $table->double('amount');
            $table->string('currency', 20)->nullable();
            $table->date('due_date');
            $table->string('order_id')->nullable();
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
        Schema::dropIfExists('customer_quote_installments');
    }
}
