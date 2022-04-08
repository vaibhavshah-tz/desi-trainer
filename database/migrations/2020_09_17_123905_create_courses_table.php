<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_category_id')->index();
            $table->string('name');
            $table->longText('description');
            $table->string('currency', 20)->nullable();
            $table->double('course_price');
            $table->double('course_special_price');
            $table->string('cover_image')->nullable();
            $table->tinyInteger('trending')->default(0)->comment('0 => Not Trending, 1 => Trending');
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
        Schema::dropIfExists('courses');
    }
}
