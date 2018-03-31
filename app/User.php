<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $fillable = [
        'username',
        'password',
        'level',
        'facility_id',
        'fname',
        'mname',
        'lname',
        'title',
        'contact',
        'email',
        'muncity',
        'province',
        'accreditation_no',
        'accreditation_validity',
        'license_no',
        'prefix',
        'picture',
        'designation',
        'status'
    ];
}
