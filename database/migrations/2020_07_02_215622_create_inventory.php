<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('inventory')){
            return true;
        }
        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('facility_id')->nullabe();
            $table->integer('encoded_by')->nullabe();
            $table->string('name')->nullabe();
            $table->integer('capacity')->nullabe();
            $table->integer('occupied')->nullabe();
            $table->integer('available')->nullabe();
            $table->string('status')->nullabe();
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
        //
    }
}
