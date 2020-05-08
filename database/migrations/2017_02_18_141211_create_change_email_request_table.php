<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangeEmailRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_email_requests', function (Blueprint $table) {
	        $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->string('token_confirmation_change', 32);
            $table->string('token_confirmation_email', 32);
            $table->string('before_email', 256);
            $table->string('next_email', 256);
            $table->boolean('confirmated')->default(false);
            $table->boolean('email_confirmed')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('change_email_requests');
    }
}
