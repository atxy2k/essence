<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('devices', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedSmallInteger('type');
            $table->unsignedSmallInteger('subtype');
            $table->string('name');
            $table->string('label');
            $table->boolean('enabled')->default(false);
            $table->dateTime('last_connection')->nullable();
            $table->timestamps();
        });

        Schema::create('device_location_history', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('device_id');
            $table->string('latitude');
            $table->string('longitude');
            $table->dateTime('date')->nullable();
            $table->timestamps();

            $table->foreign('device_id')->references('id')
                ->on('devices')->onDelete('cascade');
        });

        Schema::create('devices_access_history', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('device_id');
            $table->dateTime('old_access')->nullable();
            $table->unsignedBigInteger('device_location_history_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('device_location_history_id')->references('id')->on('device_location_history')
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices_access_history');
        Schema::dropIfExists('device_location_history');
        Schema::dropIfExists('devices');
    }
}
