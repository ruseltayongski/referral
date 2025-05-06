<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnStatusToSubopdIdInTelemedAssignDoctor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('telemed_assign_doctor', 'status')) {
            Schema::table('telemed_assign_doctor', function (Blueprint $table) {
                $table->renameColumn('status', 'subopd_id');
            });
    
            Schema::table('telemed_assign_doctor', function (Blueprint $table) {
                $table->integer('subopd_id')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telemed_assign_doctor', function (Blueprint $table) {
            $table->string('subopd_id')->change();
        });

        Schema::table('telemed_assign_doctor', function (Blueprint $table) {
            $table->renameColumn('subopd_id', 'status');
        });
    }
}
