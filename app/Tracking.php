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
        'date_transferred',
        'date_arrived',
        'date_seen',
        'mode_transportation',
        'date_accepted',
        'referred_from',
        'referred_to',
        'department_id',
        'remarks',
        'referring_md',
        'action_md',
        'status',
        'type',
        'form_id',
        'form_type',
        'walkin',
        'source',
        'telemedicine',
        'appointmentId',
        'subopd_id'
    ];

    public function activities(){
        return $this->hasMany(Activity::class, 'patient_id', 'patient_id');
    }
}
