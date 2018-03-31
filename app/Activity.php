<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';
    protected $fillable = [
        'patient_id',
        'code',
        'date_referred',
        'date_arrived',
        'date_seen',
        'referred_from',
        'referred_to',
        'remarks',
        'status'
    ];
}
