<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';
    protected $guarded = array();

    public function patient() {
        return $this->belongsTo(Patients::class, 'patient_id', 'id');
    }

    // public function municipal()
    // {
    //     return $this->belongsTo(Muncity::class, 'muncity', 'id');
    // }

    public function referredFrom() {
        return $this->belongsTo(Facility::class, 'referred_from', 'id');
    }

    public function labRequest() {
        return $this->hasMany(labRequest::class, 'activity_id', 'id');
    }

  public function tracking() {
    return $this->belongsTo(Tracking::class, 'patient_id', 'patient_id');
  }
}
