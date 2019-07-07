<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('patient_form')){
            return true;
        }
        Schema::table('patient_form', function (Blueprint $table) {
            $table->integer('department_id')->after('referred_to');
        });

        Schema::table('pregnant_form', function (Blueprint $table) {
            $table->integer('department_id')->after('referred_to');
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
