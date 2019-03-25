<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $fillable =[
    		'type',
    		'notifiable_id',
    		'notifiable_type',
    		'data',
    ];

    protected $hidden = ['read_at'];

    protected $dates = ['read_at'];
}
