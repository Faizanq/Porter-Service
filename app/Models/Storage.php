<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Storage extends Model
{
    use SoftDeletes;

    protected $fillable =[
    		'address','latitude','longitude'
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    public function getReasonAttribute($name){
        return ucwords($name);
    }
}
