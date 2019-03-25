<?php

namespace App\Models;

use App\Models\Apply;
use App\Models\Bookmark;
use App\Models\Company;
use App\Models\Cv;
use App\Models\Experience;
use App\Models\Job;
use App\Models\UserLanguage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,SoftDeletes,HasApiTokens,Billable;

    const FACEBOOK = 'F';
    const GOOGLE   = 'G';
    const TWITER   = 'T';
    const LINKEDIN = 'L'; 

    const USER   = '1';
    const DRIVER = '2';


    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name','email', 'password',
        'verify_email_token','address',
        'mobile_number','country_code','is_accepted_terms',
        'latitude','longitude','social_id','social_type',
        'state','city','dob','profile_image','language'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        'verify_email_token',
        'deleted_at','otp','status','user_type','address',
        'email_verification_token_timeout',
        'state','city','zip_code','social_id','social_type','image_url'
        ];


    public function scopeGetByDistance($query,$lat, $lng, $distance){
        
      $results = DB::select(DB::raw('SELECT *, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat .') ) * sin( radians(latitude) ) ) ) AS distance FROM jobs HAVING distance < ' . $distance . ' ORDER BY distance') );

        if(!empty($results)) {

            $ids = [];

            //Extract the id's
            foreach($results as $q) {
               array_push($ids, $q->id);
            }
            return $query->whereIn('id',$ids);

        }
        // dd($results,$lat, $lng);
        return $query;
    }


    // public function getProfileImageAttribute($image){

    //     return $image != '' && $image != null ? url('img').'/'.$image : '';
    // }


    public function getProfileImageAttribute($image){

        $image = $image != '' && $image != null ? url('img').'/'.$image : '';
        if($image == '')
         $image =  $this->attributes['image_url'] != '' && $this->attributes['image_url'] != null ? $this->attributes['image_url'] : '';

        return $image;
    }

    public function getAddressAttribute($address){

        return $address != null ?  $address :  '';
    }


    public function getDobAttribute($dob){

        return $dob != null ?  $dob :  '';
    }

    public function getEmailAttribute($email){

        return $email != null ?  $email :  '';
    }

    public function getFullNameAttribute($full_name){

        return $full_name != null ?  $full_name :  '';
    }

    public function getMobileNumberAttribute($mobile_number){

        return $mobile_number != null ?  $mobile_number :  '';
    }

    public function getCountryCodeAttribute($country_code){

        return $country_code != null ?  $country_code :  '';
    }

    public function getLatitudeAttribute($latitude){

        return $latitude != null ?  $latitude : 0;
    }

    public function getLongitudeAttribute($longitude){

        return $longitude != null ?  $longitude : 0;
    }

    public function getStateAttribute($state){

        return $state != null ?  $state :  '';
    }

    public function getCityAttribute($city){

        return $city != null ?  $city :  '';
    }

    public function getZipCodeAttribute($zip_code){

        return $zip_code != null ?  $zip_code :  '';
    }

  
    public function setEmailAttribute($email){
        $this->attributes['email'] = strtolower($email);
    }

    public static function generateVerificationCode(){
        return str_random(40);
    }

    public static function generateOtpCode(){
        return rand(1000, 9999);
    }

}

