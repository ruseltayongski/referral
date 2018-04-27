<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';
    protected $fillable = [
        'patient_id',
        'code',
        'date_referred',
        'date_seen',
        'referred_from',
        'referred_to',
        'department_id',
        'referring_md',
        'remarks',
        'status',
        'action_md'
    ];
}
