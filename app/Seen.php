<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seen extends Model
{
    protected $table = 'seen';
    protected $fillable = [
        'tracking_id',
        'facility_id',
        'user_md'
    ];
}
