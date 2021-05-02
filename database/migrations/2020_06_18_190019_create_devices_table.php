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
            $table->string('label');
            $table->string('name');
            $table->boolean('enabled')->default(false);
            $table->dateTime('last_connection')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('platform')->nullable();
            $table->string('webdriver')->nullable();
            $table->string('language')->nullable();
            $table->string('color_depth')->nullable();
            $table->string('device_memory')->nullable();
            $table->string('hardware_concurrency')->nullable();
            $table->string('timezone')->nullable();
            $table->boolean('session_storage')->nullable();
            $table->boolean('localstorage')->nullable();
            $table->boolean('indexed_db')->nullable();
            $table->boolean('open_database')->nullable();
            $table->string('cpu_class')->nullable();
            $table->timestamps();
        });

        Schema::create('device_location_history', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('device_id')->nullable();
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

        Schema::create('authorized_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('device_id');
            $table->unsignedBigInteger('application_id');
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authorized_apps');
        Schema::dropIfExists('devices_access_history');
        Schema::dropIfExists('device_location_history');
        Schema::dropIfExists('devices');
    }
}
