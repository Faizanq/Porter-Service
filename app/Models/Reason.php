<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reason extends Model
{
    use SoftDeletes;

    protected $fillable =[
    		'reason',
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    public function getReasonAttribute($name){
        return ucwords($name);
    }
}
