<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposal_trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals');
            $table->foreignId('trainer_id')->constrained('trainers');
            $table->tinyInteger('action')->default(config('constants.PROPOSAL.PENDING'))->comment('0 => Pending, 1 => Accepted, 2 => Denied');
            $table->string('denied_reason')->nullable();
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
        Schema::dropIfExists('proposal_trainers');
    }
}
