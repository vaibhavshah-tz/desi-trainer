<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('trainer_id')->nullable()->constrained('trainers');
            $table->foreignId('timezone_id')->constrained('timezones');
            $table->string('meeting_title');
            $table->date('date');
            $table->time('time');
            $table->string('meeting_url');
            $table->integer('meeting_timestamp');
            $table->tinyInteger('create_meeting_with')->comment('1 => Customer, 2 => Trainer, 3 => Both')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 => Active, 0 => Inactive, 2 => Canceled');
            $table->softDeletes();
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
        Schema::dropIfExists('meetings');
    }
}
