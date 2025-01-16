<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentSubcategoryInConfigSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('config_schedule', function (Blueprint $table) {
            //
            $table->string('deparment_subcategory')->after('department_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('config_schedule', function (Blueprint $table) {
            //
            $table->dropColumn('deparment_subcategory');
        });
    }
}
