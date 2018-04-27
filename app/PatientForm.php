<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientForm extends Model
{
    protected $table = 'patient_form';
    protected $fillable = [
        'code',
        'unique_id',
        'referring_facility',
        'referred_to',
        'department_id',
        'time_referred',
        'time_transferred',
        'patient_id',
        'case_summary',
        'reco_summary',
        'diagnosis',
        'reason',
        'referring_md',
        'referred_md'
    ];
}
