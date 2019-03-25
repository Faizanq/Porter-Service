<?php

namespace App\Models;

use App\Models\Image;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable =[
    		'user_id','driver_id','bagage','date','order_type',
    		'time','pickup_address','pickup_latitude',
    		'pickup_longitude','status','price','distance','is_pony_service'
    ];

    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    public function getPickupAddressAttribute($pickup_address){

        return $pickup_address != null ?  $pickup_address :  '';
    }

    public function getDropoffAddressAttribute($dropoff_address){

        return $dropoff_address != null ?  $dropoff_address :  '';
    }

    public function getPickupLatitudeAttribute($pickup_latitude){

        return $pickup_latitude != null ?  $pickup_latitude :  0;
    }

    public function getQrImageAttribute($image){

        return $image != '' && $image != null ? url('/public/img/qrcode').'/'.$image : '';

    }

    public function getPickupLongitudeAttribute($pickup_longitude){

        return $pickup_longitude != null ?  $pickup_longitude :  0;
    }

    public function getDropoffLatitudeAttribute($dropoff_latitude){

        return $dropoff_latitude != null ?  $dropoff_latitude :  0;
    }

    public function getDropoffLongitudeAttribute($pickup_longitude){

        return $pickup_longitude != null ?  $pickup_longitude :  0;
    }

    public function getStatusAttribute($status){

        return $status != null ?  $status :  '';
    }

    public function getOrderTypeAttribute($order_type){

        return $order_type != null ?  $order_type :  '';
    }

    public function getDistanceAttribute($distance){

        return $distance != null ?  $distance :  '';
    }

    public function getPriceAttribute($price){

        return $price != null ?  $price :  '';
    }

    public function user(){
      return $this->belongsTo(User::class,'user_id','id');
    }

    public function driver(){
      return $this->belongsTo(User::class,'driver_id','id');
    }

    public function Images(){
      return $this->hasMany(Image::class);
    }

    public function laststatus(){
      return $this->hasOne(OrderStatus::class);
    }



}
