<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrimarySkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('primary_skills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_category_id')->index();
            $table->string('name');
            $table->tinyInteger('status')->default(1)->comment('1 => Active, 0 => Inactive');
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
        Schema::dropIfExists('primary_skills');
    }
}
