<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentSchedule extends Model
{
    protected $table = 'appointment_schedule';
    protected $guarded = array();

    // Using fillable
    //protected $fillable = ['appointed_date', 'appointed_time', 'facility_id', 'department_id', 'slot', 'created_by'];

    public function createdBy() {
        return $this -> belongsTo(User::class, 'created_by', 'id');
    }

    public function facility() {
        return $this -> belongsTo(Facility::class, 'facility_id', 'id');
    }

    public function department(){
        return $this -> belongsTo(Department::class, 'department_id', 'id');
    }
}
