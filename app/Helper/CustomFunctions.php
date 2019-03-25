<?php

namespace App\Helper;
use App\Models\Notification;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client;

class CustomFunctions {

  //To return nullyfy the values
  public static function nullyfy($object){

    $result = [];
    
    foreach ($object->getAttributes() as $attribute => $value) {

    		if($object[$attribute] == null)
    			$result[$attribute] = '';

        else $result[$attribute] = (string)$value;
    }
    return $object;

  }//nullyfy ends here

  public static function languagesLevel(){
  	return [
  	'Beginner','Intermidiate','Advance','Native'
  	];
  }

   public static function availabilityType(){
    return [
    'Immediate Start',
    'Not Immediate',
    'Not Available'
    ];
  }

   public static function employmentType(){
    return [
    1=>'Full Time',
    2=>'Part Time',
    3=>'Temporary',
    4=>'Freelance',
    5=>'Contract'
    ];
  }

  public static function salaryType(){
    return [
    1=>'Hourly',
    2=>'Monthly',
    3=>'Yearly'];
  }



  public static function pushNotification($ids,$data){


    // API access key from Google API's Console
    define( 'API_ACCESS_KEY', 'AIzaSyAJyi1SIn1_UOaXN-2k1YZuxiav6BPayYY' );
    $registrationIds = $ids;
    // prep the bundle

    $fields = array
    (
      // 'registration_ids'  => $ids,
      'to'  => $ids,
      'data'      => $data
    );
     
    $headers = array
    (
      'Authorization: key=' . API_ACCESS_KEY,
      'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    curl_close( $ch );
    return  $result;
    }



  public static function calculate_distance($lat1, $lon1, $lat2, $lon2, $unit='K') { 

  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ceil(($miles * 1.709344)); //1.609344
  } 
  else if ($unit == "N") {
      return ceil(($miles * 0.8684));
  } 
  else {
        return ceil($miles);
      }
  }//Distance function end here


  public static function sendSms($number='+918200450064',$otp)
  {
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $appSid     = config('app.twilio')['TWILIO_APP_SID'];
        $client = new Client($accountSid, $authToken);
        try
        {
            // Use the client to do fun stuff like send text messages!
            $client->messages->create(
            // the number you'd like to send the message to
                "$number",
           array(
                 // A Twilio phone number you purchased at twilio.com/console
                 'from' => '+19739658808',
                 // the body of the text message you'd like to send
                 'body' => 'Hey Porter! your OTP is '+$otp
             )
         );
       }
       catch (Exception $e)
       {
            echo "Error: " . $e->getMessage();
       }
    }


    public static function StoreNotification($from,$to,$to_user,$type='order_status_updated',$data)
    {

        try
       {

        $notification = new Notification;
        $notification->type = $to_user;
        $notification->notifiable_id = $to;
        $notification->notifiable_type = $type;
        $notification->data = json_encode($data);
        $notification->save();
        
       }
       catch (Exception $e)
       {
            echo "Error: " . $e->getMessage();
       }
    }


}