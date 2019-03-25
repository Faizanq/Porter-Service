<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $fillable =[
    		'name','state_id'
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];
}
