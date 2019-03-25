<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable =[
    		'order_id','status','driver_id','bagage'
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];


    public function driver(){
      return $this->belongsTo(User::class,'driver_id','id');
    }

    public function order(){
      return $this->belongsTo(User::class,'driver_id','id');
    }
}
