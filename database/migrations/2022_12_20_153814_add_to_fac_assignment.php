<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToFacAssignment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facility_assignment', function (Blueprint $table) {
            $table->integer('user_id')->after('username');
            $table->integer('facility_id')->after('user_id');
            $table->integer('department_id')->after('facility_code');
            $table->string('status')->after('email');
            $table->dateTime('last_login')->after('status');
            $table->string('login_status')->after('last_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fac_assignment', function (Blueprint $table) {
            //
        });
    }
}
