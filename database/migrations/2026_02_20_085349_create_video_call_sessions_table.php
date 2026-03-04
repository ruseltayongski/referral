<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoCallSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_call_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('patient_code')->index();
            $table->enum('refer_status', ['referring','accepting'])->index();
            $table->enum('status', ['onboard','leave'])->index;
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('facility_id')->index();

            $table->timestamps();

            $table->index(['patient_code', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_call_sessions');
    }
}
