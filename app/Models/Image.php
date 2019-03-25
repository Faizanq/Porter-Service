<?php

namespace App\Models;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable =[
    		'image','order_id','driver_id'
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    public function orders(){
      return $this->belongsTo(Order::class,'order_id','id');
    }

    public function driver(){
      return $this->belongsTo(User::class,'driver_id','id');
    }
}
