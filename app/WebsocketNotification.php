<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsocketNotification extends Model
{
    protected $table = 'websocket_notifications';

    protected $guarded = [];

    protected $dates = ['occurred_at', 'read_at'];
}