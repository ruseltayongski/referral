<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    protected $table = 'patients';
    protected $fillable = [
        'unique_id',
        'fname',
        'mname',
        'lname',
        'dob',
        'sex',
        'civil_status',
        'phic_id',
        'phic_status',
        'brgy',
        'muncity',
        'province',
        'address',
        'tsekap_patient'
    ];
}
