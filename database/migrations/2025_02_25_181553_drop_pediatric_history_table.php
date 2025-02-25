<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPediatricHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pediatric_history');
        Schema::create('pediatric_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->nullable();
            $table->string('prenatal_a')->nullable();
            $table->string('prenatal_g')->nullable();
            $table->string('prenatal_p')->nullable();
            $table->string('prenatal_radiowith_or_without')->nullable();
            $table->string('prenatal_with_maternal_illness')->nullable();
            $table->string('natal_born_at')->nullable();
            $table->string('natal_born_address')->nullable();
            $table->string('natal_by')->nullable();
            $table->string('natal_via')->nullable();
            $table->string('natal_indication')->nullable();
            $table->string('natal_term')->nullable();
            $table->string('natal_weight')->nullable();
            $table->string('natal_br')->nullable();
            $table->string('natal_with_good_cry')->nullable();
            $table->string('natal_other_complications')->nullable();
            $table->string('post_natal_bfeed')->nullable();
            $table->string('post_natal_bfeedx_month')->nullable();
            $table->string('post_natal_formula_feed')->nullable();
            $table->string('post_natal_ffeed_specify')->nullable();
            $table->string('post_natal_started_semifoods')->nullable();
            $table->string('post_natal_bcg')->nullable();
            $table->string('post_natal_dpt_opv_x')->nullable();
            $table->string('post_dpt_doses')->nullable();
            $table->string('post_natal_hepB_cbox')->nullable();
            $table->string('post_natal_hepB_x_doses')->nullable();
            $table->string('post_natal_immu_measles_cbox')->nullable();
            $table->string('post_natal_mmr_cbox')->nullable();
            $table->string('post_natal_others_cbox')->nullable();
            $table->string('post_natal_others')->nullable();
            $table->string('post_natal_development_milestones')->nullable();
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
       
    }
}
