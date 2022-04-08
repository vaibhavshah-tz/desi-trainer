<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('trainer_id')->nullable()->constrained('trainers');
            $table->unsignedBigInteger('course_category_id')->index()->nullable();
            $table->unsignedBigInteger('course_id')->index()->nullable();
            $table->foreignId('timezone_id')->constrained('timezones');
            $table->foreignId('ticket_type_id')->constrained('ticket_types');
            $table->string('ticket_id')->unique();
            $table->string('other_course_category')->nullable();
            $table->string('other_course')->nullable();
            $table->string('other_primary_skill')->nullable();
            $table->longText('message')->nullable();
            $table->tinyInteger('is_for_employee')->default(0)->comment('1 => Yes, 0 => No');
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('ticket_timestamp')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 => Pending, 2 => Unassigned, 3 => In progress, 4 => Assigned, 5 => Closed, 6 => Inactive');
            $table->timestamps();
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
