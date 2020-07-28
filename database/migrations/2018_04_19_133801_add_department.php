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
        Schema::table('facility', function (Blueprint $table) {
            //additional field for facility table
            //for hospital
            $table->string('officer_contact_number')->nullable();
            $table->string('officer_email_address')->nullable();
            //for LGU
            $table->string('mayor')->nullable();
            $table->string('mho')->nullable();
            $table->string('eoc')->nullable();
            $table->string('disease_surveillance_coordinator')->nullable();
            $table->string('coordinator_contact_number')->nullable();
            $table->string('coordinator_email_address')->nullable();
            $table->string('drrmh')->nullable();
            $table->string('pnp')->nullable();
            $table->string('fire')->nullable();
            $table->string('mswd')->nullable();
            $table->string('mayors_office')->nullable();
            $table->string('public_info_number')->nullable();
            $table->string('city_health_office')->nullable();
            //for CIU
            $table->string('type_of_facility')->nullable();
            //FOR SAR-COV-2 Testing Laboratory Provider Profile
            $table->string('category')->nullable();
            //for rapid test in accredited RTD
            $table->string('services')->nullable();
            //for licensed dialysis center
            $table->string('classification')->nullable();
        });

        Schema::create('other_agencies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('encoded_by');
            $table->string('agencies');
            $table->string('address');
            $table->string('contact');
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
