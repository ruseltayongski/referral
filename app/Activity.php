<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';
    protected $guarded = array();

    // Relationship with PrescribedPrescription based on 'id' column
    public function prescribedPrescriptions()
    {
        return $this->hasMany(PrescribedPrescription::class, 'prescribed_activity_id', 'id');
    }
}
