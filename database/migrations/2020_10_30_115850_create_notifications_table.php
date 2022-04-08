<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('notificationtable_id')->nullable();
            $table->string('notificationtable_type')->nullable();
            $table->bigInteger('sender_id')->nullable();
            $table->bigInteger('receiver_id')->nullable();
            $table->tinyInteger('sender_type')->nullable()->comment('1=> User, 2=> Customer, 3=> Trainer');
            $table->tinyInteger('receiver_type')->nullable()->comment('1=> User, 2=> Customer, 3=> Trainer');
            $table->string('title')->nullable();
            $table->longText('message');
            $table->string('redirection_type')->nullable();
            $table->tinyInteger('is_read')->default(config('constants.UNREAD_NOTIFICATION'))->comment('1 => Read, 0 => Unread');
            $table->longText('push_notification')->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
