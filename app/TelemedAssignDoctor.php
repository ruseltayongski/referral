<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelemedAssignDoctor extends Model
{
    protected $table = 'telemed_assign_doctor';
    protected $guarded = array();

    public function assignDoctor(){
        return $this -> belongsTo (User::class, 'doctor_id', 'id');
    }

}