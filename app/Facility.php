<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facility';
    protected $guarded = array();



    //------------------------------------------------
    public function opdFacilities()
    {
        return $this->belongsTo(User::class, 'id', 'facility_id');
    }
    //------------------------------------------------



}


