<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PregnantForm extends Model
{
    protected $table = 'pregnant_form';
    protected $fillable = [
        'unique_id',
        'code',
        'referring_facility',
        'referred_by',
        'record_no',
        'referred_date',
        'referred_to',
        'arrival_date',
        'health_worker',
        'patient_woman_id',
        'woman_reason',
        'woman_major_findings',
        'woman_before_treatment',
        'woman_before_given_time',
        'woman_during_transport',
        'woman_transport_given_time',
        'woman_information_given',
        'patient_baby_id',
        'baby_reason',
        'baby_major_findings',
        'baby_last_feed',
        'baby_before_treatment',
        'baby_before_given_time',
        'baby_during_transport',
        'baby_transport_given_time',
        'baby_information_given'
    ];
}
