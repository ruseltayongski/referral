<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingIdToReferralDetailTables extends Migration
{
    private $tables = [
        'past_medical_history',
        'pediatric_history',
        'pertinent_laboratory',
        'personal_and_social_history',
        'review_of_system',
        'nutritional_status',
        'glasgow_coma_scale',
        'latest_vital_signs',
        'obstetric_and_gynecologic_history',
        'pregnancy',
    ];

    public function up()
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('tracking_id')->nullable()->index();
            });
        }
    }

    public function down()
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['tracking_id']);
                $table->dropColumn('tracking_id');
            });
        }
    }
};