<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnOthersInReviewOfSystems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('review_of_system', function (Blueprint $table) {
            $table->text('skin_others')->nullable()->after('skin');
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
            $table->dropColumn('skin_others');
        });
    }
}
