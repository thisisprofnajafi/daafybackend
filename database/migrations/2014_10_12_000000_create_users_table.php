<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->longText('bio')->nullable();
            $table->longText('description')->nullable();
            $table->integer('height')->nullable();
            $table->string('body_type')->nullable();
            $table->string('drink')->nullable();
            $table->string('smoke')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('living_status')->nullable();
            $table->string('relation_status')->nullable();
            $table->string('education_status')->nullable();
            $table->longText('avatar')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_setup')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('reset_code')->nullable();
            $table->timestamp('reset_expire')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
