<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = 'tracking';
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
