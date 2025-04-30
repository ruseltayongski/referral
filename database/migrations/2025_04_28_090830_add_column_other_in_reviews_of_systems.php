<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnOtherInReviewsOfSystems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('review_of_system', function (Blueprint $table) {
            $table->text('head_others')->nullable()->after('head');
            $table->text('eyes_others')->nullable()->after('eyes');
            $table->text('ears_others')->nullable()->after('ears');
            $table->text('nose_others')->nullable()->after('nose_or_sinuses');
            $table->text('mouth_others')->nullable()->after('mouth_or_throat');
            $table->text('neck_others')->nullable()->after('neck');
            $table->text('breast_others')->nullable()->after('breast');
            $table->text('respiratory_others')->nullable()->after('respiratory_or_cardiac');
            $table->text('gastrointestinal_others')->nullable()->after('gastrointestinal');
            $table->text('urinary_others')->nullable()->after('urinary');
            $table->text('peripheral_vascular_others')->nullable()->after('peripheral_vascular');
            $table->text('musculoskeletal_others')->nullable()->after('musculoskeletal');
            $table->text('neurologic_others')->nullable()->after('neurologic');
            $table->text('hematologic_others')->nullable()->after('hematologic');
            $table->text('endocrine_others')->nullable()->after('endocrine');
            $table->text('psychiatric_others')->nullable()->after('psychiatric');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('review_of_system', function (Blueprint $table) {
            //
        });
    }
}
