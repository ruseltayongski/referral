<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cofig_schedule extends Model
{
    protected $table = 'config_schedule';
    protected $guarded = array();

    public function appointmentSchedules(){
        return $this->hasMany(AppointmentSchedule::class, 'configId','id');
    }
}