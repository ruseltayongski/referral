<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facility';
    protected $fillable = [
        'name',
        'address',
        'brgy',
        'muncity',
        'province',
        'contact',
        'email',
        'status'
    ];
}
