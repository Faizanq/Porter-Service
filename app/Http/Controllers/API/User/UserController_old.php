<?php
namespace App\Http\Controllers\API\User;

use App\Http\Controllers\API\Base\ApiController;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Storage;
use App\Models\User;
use App\Notifications\MarkdownNotification;
use Carbon\Carbon;
use CustomFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Braintree_ClientToken;
use Braintree_Transaction;

class UserController extends ApiController
{

   

     /**
     * Login user if exits else send the OTP
     *
     * @param  [string] dial_code
     * @param  [string] mobile_no
     * @param  [boolean] language
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */

    public function login(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string',
            'mobile_no' => 'required|string',       
        ]);

        //@todo need device_id and device_type

        // dd($request);

        $message = 'OTP sent to your mobile number';
        $user = User::where(['country_code'=>$request->country_code,'mobile_number'=>$request->mobile_no])->first();

        if($user){

            if($user->is_mobile_verify == 'N'){
                return $this->ErrorResponse('Please verify your mobile number');
            }
            $user->otp = User::generateOtpCode();
            $user->save();

            // try{
            // CustomFunctions::sendSms($user->country_code.$user->mobile_number,$user->otp);
            // }catch(Exception $e){

            // }

        }else{           

            $message = 'Success';

            $user = new User;

            if(!empty($request->language)){
                $user->language = $request->language;
            }

            if(!empty($request->country_code)){
                $user->country_code = $request->country_code;
            }

            if(!empty($request->mobile_no)){
                $user->mobile_number = $request->mobile_no;
            }
            $user->otp = User::generateOtpCode();
            $user->user_type = 1;
            //  try{
            // CustomFunctions::sendSms($user->country_code.$user->mobile_number,$user->otp);
            // }catch(Exception $e){

            // }
        }

        if(!empty($request->language)){
            $user->language = $request->language;
        }

        $user->save();


        $tokenResult = $user->createToken('secret');

        $token = $tokenResult->token;  


        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);

        if (!empty($request->device_id))
            $token->device_id = $request->device_id;
        
        $token->save();

        $tokenArray = [
        'access_token' => $tokenResult->accessToken,
        'token_type' => 'Bearer',
        'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];

        return $this->LoginResponse($user,$tokenArray,$message);
    }


	/**
     * Create Order
     *
     * @param  [string] bagage
     * @param  [string] date
     * @param  [string] time
     * @param  [string] isInvalid
     * @param  [string] latitude
     * @param  [string] longitude
     * @param  [string] address
     * @return [string] message
     */

    public function Post(Request $request){

    	$request->validate([
            'bagage' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'isInvalid' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'address' => 'required|string'
        ]);

    	$user = $request->user();


        //get the nearest storage
        $storage = Storage::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$request->latitude, $request->longitude, $request->latitude])
            // ->having('distance', '<', 30)
            ->orderBy('distance')
            ->first();

        $distance = CustomFunctions::calculate_distance($storage->latitude,$storage->longitude,$request->latitude,$request->longitude);

        $per_lagage_charge = Setting::find(1);
        $per_day_charge = Setting::find(2);


        if($distance <= 2){
         $zero_to_two = Setting::find(3);
         $distance_price = $distance * $zero_to_two->value;
        }
        else if ($distance <= 4){
            $two_to_four = Setting::find(4);
            $distance_price = $distance * $two_to_four->value;
        }
        else if ($distance <= 4){
            $four_to_six = Setting::find(5);
            $distance_price = $distance * $four_to_six->value;
        }
        else {
            $above_seven = Setting::find(6);
            $distance_price = $distance * $above_seven->value;
        }

        $now = time(); // or your date as well
        // $your_date = strtotime($request->date);
        $your_date = $request->date;
        $datediff = $your_date - $now;

        $day = round($datediff / (60 * 60 * 24));
        $day = $day ? $day : 1;

        $amount = 0;

        $amount += ($per_lagage_charge->value * $request->bagage) + $distance_price + $per_day_charge->value *  $day;


        $order = new Order;

        $order->user_id = $user->id;


        if(!empty($request->transaction_id))
             $order->payment_id = $request->transaction_id;

        if(!empty($request->bagage))
        	 $order->bagage = $request->bagage;

        if(!empty($request->date))
        	 $order->date = $request->date;

        if(!empty($request->time))
        	 $order->time = $request->time;

        if(!empty($request->isInvalid))
        	 $order->isInvalid = $request->isInvalid;

        if(!empty($request->address))
        	 $order->pickup_address = $request->address;

        if(!empty($request->latitude))
        	 $order->pickup_latitude = $request->latitude;

        if(!empty($request->longitude))
        	 $order->pickup_longitude = $request->longitude;


        if(!empty($storage->address))
             $order->dropoff_address = $storage->address;

        if(!empty($storage->latitude))
             $order->dropoff_latitude = $storage->latitude;

        if(!empty($storage->longitude))
             $order->dropoff_longitude = $storage->longitude;

        $order->distance = $distance.' '.'KM';
        $order->price = $amount;
        $order->save();



        //Now lets generate the qrcode

        $name = str_random();
          QrCode::format('png')->size(100)->generate('PORTER-'.$order->id, '../public/img/qrcode/'.$name.'.png');

        $order->qr_image = $name.'.png';

        $order->save();

        //Lets find the porter
       $porter = User::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$request->latitude, $request->longitude, $request->latitude])
            // ->having('distance', '<', 30)
            ->orderBy('distance')
            ->where(['online'=>'Y','is_connected'=>'Y','status'=>'Y','user_type'=>2])
            ->first();

        $result['order_info']["order_id"]  =  $order->id;
        $result['order_info']["no_of_bag"]    =  $request->bagage;
        $result['order_info']["price"]    =  $amount; 
        $result['order_info']["qr_image"]  =  $order->qr_image; 
        $result['order_info']['time'] = $order->time;
        $result['order_info']['date'] = $order->date;
        $result['order_info']["distance"]          =  $order->distance; 
        $result['order_info']["starting_address"]    =  $order->pickup_address; 
        $result['order_info']["starting_latitude"]   =  $order->pickup_latitude; 
        $result['order_info']["starting_longitude"]  =  $order->pickup_longitude; 
        $result['order_info']["ending_address"]   =  $order->dropoff_address; 
        $result['order_info']["ending_latitude"]  =  $order->dropoff_latitude; 
        $result['order_info']["ending_longitude"] =  $order->dropoff_longitude;
        $result['order_info']["message"] =  'New Request';
        $result['order_info']["title"] =  'New Request';
        $result['order_info']["notification_type"] =  'request';
        $result['order_info']["vibrate"] =  '1';
        $result['order_info']["sound"] =  '1';
        $result['order_info']['order_status'] = $order->status;
        $result['order_info']['order_type'] = $order->order_type;
        $result['order_info']['isInvalid'] = $order->isInvalid;
        $result['order_info']['user_state'] = '';
        $result['order_info']['user_name'] = $order->user ? $order->user->full_name:'';
        $result['order_info']['user_state'] = '';
        $result['order_info']['user_image'] = $order->user ? $order->user->profile_image:'';
        $result['order_info']['estimated_time'] = $order->time;
        $result['order_info']['estimated_distanc'] = $order->distance;
        $result["notification_type"] =  'request';


        $devices_object = DB::table('oauth_access_tokens')->where(['user_id'=>isset($porter->id)?$porter->id:null])->where('device_id','!=',null)->get();
        

        if(!empty($devices_object)){

          $device_ids = last(array_pluck($devices_object,'device_id'));

          //Now send the notifications
           CustomFunctions::pushNotification($device_ids,$result);

        }

        //todo send the notification to drivers
        $result1["order_id"]  =  $order->id;

        return $this->SuccessResponse($result1,$message='Your request place successfully');
    }

    /**
     * Return User Order list
     * @return [Object] result
     */

    public function List(Request $request){

        $offset = isset($request->start) && $request->start !=null ? $request->start:0;
        $limit = isset($request->limit) && $request->limit !=null ? $request->limit+1:11;

    	$user = $request->user();
    	$orders = Order::where(['user_id'=>$user->id])->offset($offset)->take($limit)->orderBy('id', 'DESC')->get();

    	$result = [];
        // (1)Accept/start (2)Pick up / Arrived  / (3)Delivered
    //(4) Cancel
        $status = [
            0=>'Pending',
            1=>'Accept',
            2=>'Start Pick up',
            3=>'Arrived',
            4=>'Picked up',
            5=>'Delivered',
            6=>'Cancel By User',
            7=>'Cancel By Porter',
        ];

    	$i = 0;
    	foreach ($orders as $key => $order) {
    		$result['order_list'][$i]['id'] = $order->id;
            $result['order_list'][$i]['order_id'] = $order->id;
            $result['order_list'][$i]['bagage'] = $order->bagage;
            $result['order_list'][$i]['date'] = $order->date;
    		$result['order_list'][$i]['time'] = $order->time;
            $result['order_list'][$i]['status_code'] = $order->status;
            $result['order_list'][$i]['status'] = $status[$order->status];
    		$result['order_list'][$i]['isInvalid'] = $order->isInvalid;
    		$result['order_list'][$i]['amount'] = $order->price;
    		$result['order_list'][$i]['address'] = $order->pickup_address;
    		$result['order_list'][$i]['latitude'] = $order->pickup_latitude;
            $result['order_list'][$i]['longitude'] = $order->pickup_longitude;
    		$result['order_list'][$i]['qr_image'] = $order->qr_image;
    	$i++;
    	}

    	$is_last = 1;

        $message = 'No order found';
        if($i)
            $message = 'order list';

        if($i >= $limit){
            unset($result['order_list'][$i-1]);
            $is_last = 0;

        }
    	return $this->SuccessList($result,$message,200,$is_last);

   }


    public function Detail(Request $request){


        $request->validate([
            'order_id' => 'required|string',
        ]);


        $user = $request->user();

        $order = Order::where(['user_id'=>$user->id,'id'=>$request->order_id])
        ->first();

        $status = [
            0=>'Pending',
            1=>'Accept',
            2=>'Start Pick up',
            3=>'Arrived',
            4=>'Picked up',
            5=>'Delivered',
            6=>'Cancel By User',
            7=>'Cancel By Porter',
        ];

        if(!$order)
            $this->ErrorResponse('No such order found');

        $result = [];

            $result['id'] = $order->id;
            $result['order_id'] = $order->id;
            $result['bagage'] = $order->bagage;
            $result['date'] = $order->date;
            $result['time'] = $order->time;
            $result['status'] = $status[$order->status];
            $result['isInvalid'] = $order->isInvalid;
            $result['amount'] = $order->price;
            $result['address'] = $order->pickup_address;
            $result['latitude'] = $order->pickup_latitude;
            $result['longitude'] = $order->pickup_longitude;


            $result['Storeage_address'] = $order->dropoff_address;
            $result['Storeage_latitude'] = $order->dropoff_latitude;
            $result['Sotrage_longitude'] = $order->dropoff_longitude;

            $result['qr_image'] = $order->qr_image;
            
        $message = 'order detail';

        return $this->SuccessResponse($result,$message,200);

   }



    /**
     * Cost Calculation
     *
     * @param  [string] bagage
     * @param  [string] date
     * @param  [string] time
     * @param  [string] isInvalid
     * @param  [string] latitude
     * @param  [string] longitude
     * @param  [string] address
     * @return [string] message
     */

    public function Cost(Request $request){

        $request->validate([
            'bagage' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'isInvalid' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'address' => 'required|string'
        ]);

        //get the nearest storage
        $storage = Storage::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$request->latitude, $request->longitude, $request->latitude])
            // ->having('distance', '<', 30)
            ->orderBy('distance')
            ->first();

        $distance = CustomFunctions::calculate_distance($storage->latitude,$storage->longitude,$request->latitude,$request->longitude);

        $per_lagage_charge = Setting::find(1);
        $per_day_charge = Setting::find(2);


        if($distance <= 2){
         $zero_to_two = Setting::find(3);
         $distance_price = $distance * $zero_to_two->value;
        }
        else if ($distance <= 4){
            $two_to_four = Setting::find(4);
            $distance_price = $distance * $two_to_four->value;
        }
        else if ($distance <= 4){
            $four_to_six = Setting::find(5);
            $distance_price = $distance * $four_to_six->value;
        }
        else {
            $above_seven = Setting::find(6);
            $distance_price = $distance * $above_seven->value;
        }

        $now = time(); // or your date as well
        // $your_date = strtotime($request->date);
        $your_date = $request->date;
        $datediff =  $your_date - $now;

        $day = round($datediff / (60 * 60 * 24));
        $day = $day ? $day : 1;

        $amount = 0;

        $amount += ($per_lagage_charge->value * $request->bagage) + $distance_price + $per_day_charge->value *  $day;

        if(!empty($request->date))
             $date = $request->date;

        if(!empty($request->time))
             $time = $request->time;

        if(!empty($request->isInvalid))
             $isInvalid = $request->isInvalid;

        if(!empty($request->address))
             $pickup_address = $request->address;

        if(!empty($request->latitude))
             $pickup_latitude = $request->latitude;

        if(!empty($request->longitude))
             $pickup_longitude = $request->longitude;

        $result = [];

        $result["is_porter_available"] =  'N';
        $result["porter_latitude"]     =  '';
        $result["porter_longitude"]    =  '';
        $result["porter_id"] =  '';


      //CustomFunctions::calculate_distance(22.9936,72.4987,22.7788,73.6143);

        //Lets find the porter

       $porter = User::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$request->latitude, $request->longitude, $request->latitude])
            // ->having('distance', '<', 30)
            ->orderBy('distance')
            ->where(['online'=>'Y','is_connected'=>'Y','status'=>'Y','user_type'=>1])
            ->first();

        // $porter = User::where(['online'=>'Y','is_connected'=>'Y'])->first();

        $result = Braintree_Transaction::sale([ 'amount' => '70.00', 'paymentMethodNonce' => 'tokencc_bc_skmk62_b23mts_yhmxdx_myd9d8_4sy' ]);

        dd($result);


        if($porter){
            $result["is_porter_available"] =  'Y';
            $result["porter_latitude"]     =  $porter->latitude;
            $result["porter_longitude"]    =  $porter->longitude;
            $result["porter_id"] =  $porter->id;
        }

        $result["order_id"] =  '';
        $result["bagage"]   =  $request->bagage;
        $result["amount"]   =  $amount; 
        $result["token"]    =  Braintree_ClientToken::generate();
        //todo send the notification to drivers

        return $this->SuccessResponse($result,$message='Success');
    }


    public function CancelOrder(Request $request){

         $request->validate([
            'order_id' => 'required|string',      
            // 'cancel_reason' => 'required|string',      
        ]);

        $user = $request->user();

        $order = Order::where(['driver_id'=>$user->id,'id'=>$request->order_id])
        ->first();

        if(!$order)
            return $this->ErrorResponse($message='No such order found');

        $order->status = '6';
        // $order->cancelation_message = $request->cancel_reason;
        $order->save();

        return $this->SuccessResponse([],$message='success');
    }


        public function EditProfile(Request $request)
    {

        $request->validate([
            'fullname' => 'required|string',
            'dob' => 'required|string',
            'is_notification' => 'required|string',
            'gender' => 'required|string',
        ]);

        $user = $request->user();


        if(!empty($request->latitude))
            $user->latitude = $request->latitude;

        if(!empty($request->longitude))
            $user->longitude = $request->longitude;

        if(!empty($request->fullname))
            $user->full_name = $request->fullname;

        if(!empty($request->address))
            $user->address = $request->address;

        if(!empty($request->dob))
            $user->dob = $request->dob;

        if(!empty($request->is_notification))
            $user->is_notification = $request->is_notification;

        if(!empty($request->gender))
            $user->gender = $request->gender;

        if(!empty($request->is_terms_accept))
            $user->is_accepted_terms = $request->is_terms_accept;
        
        if(!empty($request->email))
            $user->email = $request->email;

        if(!empty($request->city))
            $user->city = $request->city;

        if(!empty($request->state))
            $user->state = $request->state;

        if(!empty($request->password))
            $user->password = bcrypt($request->password);

         if(!empty($request->image)){

            $imageName = time().'.'.$request->image->getClientOriginalExtension();

            $request->profile_image->move(public_path('img'), $imageName);

            $user->profile_image = $imageName;

        }

         $user->save();


         $tokenArray = [
        'access_token' => last(explode(' ',$request->header('Authorization'))),
        'token_type' => 'Bearer',
        'expires_at' => ''
        ];


        return $this->LoginResponse($user,$tokenArray,$message='Profile update successfully');

    }


}