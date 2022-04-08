<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('chat_room_id')->constrained('chat_rooms');
            $table->tinyInteger('sender_type')->comment('1 => User, 2 => Customer, 3 => Trainer');
            $table->longText('message');
            $table->string('file')->nullable();
            $table->tinyInteger('is_read')->default(0)->comment('1 => Read, 0 => Unread');
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
        Schema::dropIfExists('chat_messages');
    }
}
