<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('facility_id')->nullabe();
            $table->integer('encoded_by')->nullabe();
            $table->integer('inventory_id')->nullabe();
            $table->integer('capacity')->nullabe();
            $table->integer('occupied')->nullabe();
            $table->integer('available')->nullabe();
            $table->time('time_created');
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
        Schema::dropIfExists('inventory_logs');
    }
}
