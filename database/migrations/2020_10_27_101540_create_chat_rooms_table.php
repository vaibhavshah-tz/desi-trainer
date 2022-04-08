<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('trainer_id')->nullable()->constrained('trainers');
            $table->string('room_id');
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
        Schema::dropIfExists('chat_rooms');
    }
}
