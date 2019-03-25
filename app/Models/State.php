<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes;

    protected $fillable =[
    		'name',
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];
}
