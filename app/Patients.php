<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    protected $table = 'patients';
    protected $guarded = array();

    public function municipal()
    {
        return $this->belongsTo(Muncity::class, 'muncity', 'id');
    }
}
