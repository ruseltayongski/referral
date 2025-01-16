<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class UpdateDepartmentIdToStringInConfigSchedule extends Migration
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
            $table->string('department_id')->nullable()->change();
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
            DB::statement('ALTER TABLE config_schedule MODIFY department_id INT DEFAULT NULL');
        });
    }
}
