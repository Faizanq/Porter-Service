<?php

namespace App\Models;

use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable,SoftDeletes;


    const VERIFIED = '1';
    const UNVERIFIED = '0';


    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','verify_email_token',
        'verified',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','verify_email_token'
    ];

    public function setNameAttribute($name){
        $this->attributes['name'] = strtolower($name);
    }
    public function getNameAttribute($name){
        return ucwords($name);
    }
    public function setEmailAttribute($email){
        $this->attributes['email'] = strtolower($email);
    }

    public function isVerified(){
        // dd($this->verified , User::VERIFIED_USER,$this->verification_token);
        $this->verified == User::VERIFIED;
    }

    public static function generateVerificationCode(){
        return str_random(40);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

}
