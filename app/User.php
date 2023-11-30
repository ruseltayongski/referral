<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $guarded = array();


    // Define the relationship with Facility
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    // Define the relationship with Department
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
