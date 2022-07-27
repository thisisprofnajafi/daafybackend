<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('height')->nullable();
            $table->string('body_type')->nullable();
            $table->string('drink')->nullable();
            $table->string('smoke')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('living_status')->nullable();
            $table->string('seeking_for')->nullable();
            $table->string('education_status')->nullable();
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
        Schema::dropIfExists('interests');
    }
}
