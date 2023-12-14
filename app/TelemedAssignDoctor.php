<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelemedAssignDoctor extends Model
{
    protected $table = 'telemed_assign_doctor';
    protected $guarded = array();

    protected $fillable = ['appointment_id', 'doctor_id', 'status', 'created_by'];

}