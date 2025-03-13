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

    public function creator(){
        
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subOpdCateg(){
        return $this->belongsTo(SubOpd::class, 'subopd_id');
    }

}
