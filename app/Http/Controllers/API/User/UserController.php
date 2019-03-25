<?php
namespace App\Http\Controllers\API\User;

use App\Http\Controllers\API\Base\ApiController;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Storage;
use App\Models\User;
use App\Models\Notification;
use App\Notifications\MarkdownNotification;
use Carbon\Carbon;
use CustomFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Braintree_ClientToken;
use Braintree_Transaction;

class UserController extends ApiController
{


    public function summery(Request $request){


        $user = $request->user();

        // $count = OrderStatus::where([
        //                 'driver_id'=>$user->id
        //                 ])
        //                 ->whereIn('status', ['5','7'])
        //                 ->get();

        $result = [];

        $result['order_count']=  Order::where(['user_id'=>$user->id])->count();

        $Notifications = Notification::where(['type'=>'U','notifiable_id'=>$user->id])->get();

        $result['notification_count'] = $Notifications->count();


       
        return $this->SuccessResponse($result,$message='success');
    }
    

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
                // return $this->ErrorResponse('OTP sent to your mobile number');

                return $this->SuccessResponse([],$message='OTP sent to your mobile number');
            }
            $user->otp = 1234;
            $user->save();
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
            $user->otp = 1234;
            $user->user_type = 1;
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
            'address' => 'required|string',
            'transation_id'=>'required|string',
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
        $distance_price = 0;


        if($distance <= 2 && $distance >=7){
         $zero_to_two = Setting::find(3);
         $distance_price += $distance * $zero_to_two->value;
        }
        else if ($distance <= 4 && $distance >=7){
            $two_to_four = Setting::find(4);
            $distance_price += $distance * $two_to_four->value;
        }
        else if ($distance <= 4 && $distance >=7){
            $four_to_six = Setting::find(5);
            $distance_price += $distance * $four_to_six->value;
        }
        else {
            $above_seven = Setting::find(6);
            $distance_price += $distance * $above_seven->value;
        }

        $now = time(); // or your date as well
        // $your_date = strtotime($request->date);
        $your_date = $request->date;
        $datediff = $your_date - $now;

        $day = round($datediff / (60 * 60 * 24));
        $day = $day ? $day : 1;

        $amount = 0;

        $amount += ($per_lagage_charge->value * $request->bagage) + $distance_price + $per_day_charge->value *  $day;


        //Lets perform the payment
        $paymentObject = Braintree_Transaction::sale([ 
                    'amount' => $amount,
                    'paymentMethodNonce' => $request->transation_id 
                    ]);

        if(!$paymentObject->success)
            $this->ErrorResponse($paymentObject->message);


        $order = new Order;

        $order->user_id = $user->id;

        if($paymentObject->success)
            $order->is_payment_received = 'Y';


        if(!empty($request->transaction_id))
             $order->payment_id = $request->transaction_id;
            
        if(!empty($request->transation_id))
             $order->payment_id = $request->transation_id;

        if(!empty($request->bagage)){
             $order->bagage = $request->bagage;
             $order->pending_bagage = $request->bagage;
         }

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

        if(!empty($request->is_storage))
             $order->is_storage = $request->is_storage;

         

        $order->distance = $distance.' '.'KM';
        $order->price = $amount;
        $order->save();

        //Now lets generate the qrcode

        $name = str_random();
          QrCode::format('png')->size(100)->generate('PORTER-'.$order->id, 'public/img/qrcode/'.$name.'.png');

        $order->qr_image = $name.'.png';

        $order->save();

        //Lets find the porter
       $porter = User::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$request->latitude, $request->longitude, $request->latitude])
            // ->having('distance', '<', 500)
            ->orderBy('distance')
            ->where(['online'=>'Y','is_connected'=>'Y','status'=>'Y','user_type'=>2])
            ->first();

        
            $result['is_job'] =  'Y';
            $result['status'] =  $order->status;
            $result["notification_type"] =  'request';
            
            $result['order_info']['cust_name'] = $order->user->full_name;
            $result['order_info']['cust_image'] = $order->user->profile_image;
            $result['order_info']['cust_address'] = $order->pickup_address;
            $result['order_info']['cust_lat'] = $order->pickup_latitude;
            $result['order_info']['cust_lng'] = $order->pickup_longitude;
            $result['order_info']['storage_address'] = $order->dropoff_address;
            $result['order_info']['storage_lat'] = $order->dropoff_latitude;
            $result['order_info']['storeage_lng'] = $order->dropoff_longitude;

            $support_call = Setting::find(7);
            $result['order_info']['support_call'] = isset($support_call) ? $support_call->value : '';
            //$result['order_info']["notification_type"] =  'request';


            $result['order_info']['id'] = $order->id;
            $result['order_info']['order_id'] = $order->id;
            $result['order_info']['bagage'] = $order->bagage;
            $result['order_info']['date'] = $order->date;
            $result['order_info']['time'] = $order->time;
            $result['order_info']['status'] = $order->status;
            $result['order_info']['order_type'] = $order->order_type;
            $result['order_info']['isInvalid'] = $order->isInvalid;
            $result['order_info']['amount'] = $order->price;
            $result['order_info']['address'] = $order->pickup_address;
            $result['order_info']['latitude'] = $order->pickup_latitude;
            $result['order_info']['longitude'] = $order->pickup_longitude;
            $result['order_info']['qr_image'] = $order->qr_image;


        $devices_object = DB::table('oauth_access_tokens')->where(['user_id'=>isset($porter->id)?$porter->id:null])->where('device_id','!=',null)->get();

        if(!empty($devices_object)){
            
            // dd($devices_object);

           $device_ids = last(array_pluck($devices_object,'device_id'));
        //   retrun $device_ids;
          //Now send the notifications
          CustomFunctions::pushNotification($device_ids,$result);

        }

        $result1["order_id"]  =  $order->id;

        return $this->SuccessResponse($result1,$message='Your request place successfully');
    }



    /**
     * Create Pony Order
     *
     * @param  [string] bagage
     * @param  [string] date
     * @param  [string] time
     * @param  [string] isInvalid
     * @param  [string] transation_id
     * @param  [string] pickup_latitude
     * @param  [string] pickup_longitude
     * @param  [string] pickup_address
     * @param  [string] dropoff_latitude
     * @param  [string] dropoff_longitude
     * @param  [string] dropoff_address
     * @return [string] message
     */

    public function PostPony(Request $request){

        $request->validate([
            'bagage' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'isInvalid' => 'required|string',
            'pickup_latitude' => 'required|string',
            'pickup_longitude' => 'required|string',
            'pickup_address' => 'required|string',
            'dropoff_latitude' => 'required|string',
            'dropoff_longitude' => 'required|string',
            'dropoff_address' => 'required|string',
            'transation_id'=>'required|string',
        ]);

        $user = $request->user();

        $distance = CustomFunctions::calculate_distance($request->pickup_latitude,$request->pickup_longitude,$request->dropoff_latitude,$request->dropoff_longitude);

        $per_lagage_charge = Setting::find(1);
        $per_day_charge = Setting::find(2);
        $distance_price = 0;


        if($distance <= 2 && $distance >=7){
         $zero_to_two = Setting::find(3);
         $distance_price += $distance * $zero_to_two->value;
        }
        else if ($distance <= 4 && $distance >=7){
            $two_to_four = Setting::find(4);
            $distance_price += $distance * $two_to_four->value;
        }
        else if ($distance <= 4 && $distance >=7){
            $four_to_six = Setting::find(5);
            $distance_price += $distance * $four_to_six->value;
        }
        else {
            $above_seven = Setting::find(6);
            $distance_price += $distance * $above_seven->value;
        }

        $now = time(); // or your date as well
        // $your_date = strtotime($request->date);
        $your_date = $request->date;
        $datediff = $your_date - $now;

        $day = round($datediff / (60 * 60 * 24));
        $day = $day ? $day : 1;

        $amount = 0;

        $amount += ($per_lagage_charge->value * $request->bagage) + $distance_price + $per_day_charge->value *  $day;


        //Lets perform the payment
        $paymentObject = Braintree_Transaction::sale([ 
                    'amount' => $amount,
                    'paymentMethodNonce' => $request->transation_id 
                    ]);

        if(!$paymentObject->success)
            $this->ErrorResponse($paymentObject->message);


        $order = new Order;

        $order->user_id = $user->id;

        if($paymentObject->success)
            $order->is_payment_received = 'Y';


        $order->is_pony_service = 'Y';


        if(!empty($request->transaction_id))
             $order->payment_id = $request->transaction_id;
            
        if(!empty($request->transation_id))
             $order->payment_id = $request->transation_id;

        if(!empty($request->bagage)){
             $order->bagage = $request->bagage;
             $order->pending_bagage = $request->bagage;
         }

        if(!empty($request->date))
             $order->date = $request->date;

        if(!empty($request->time))
             $order->time = $request->time;

        if(!empty($request->isInvalid))
             $order->isInvalid = $request->isInvalid;


        if(!empty($request->pickup_address))
             $order->pickup_address = $request->pickup_address;

        if(!empty($request->pickup_latitude))
             $order->pickup_latitude = $request->pickup_latitude;

        if(!empty($request->pickup_longitude))
             $order->pickup_longitude = $request->pickup_longitude;


        if(!empty($request->dropoff_address))
             $order->dropoff_address = $request->dropoff_address;

        if(!empty($request->dropoff_latitude))
             $order->dropoff_latitude = $request->dropoff_latitude;

        if(!empty($request->dropoff_longitude))
             $order->dropoff_longitude = $request->dropoff_longitude;

        if(!empty($request->is_storage))
             $order->is_storage = $request->is_storage;

         

        $order->distance = $distance.' '.'KM';
        $order->price = $amount;
        $order->save();

        //Now lets generate the qrcode

        $name = str_random();
          QrCode::format('png')->size(100)->generate('PORTER-'.$order->id, 'public/img/qrcode/'.$name.'.png');

        $order->qr_image = $name.'.png';

        $order->save();

        //Lets find the porter
       $porter = User::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$request->latitude, $request->longitude, $request->latitude])
            // ->having('distance', '<', 500)
            ->orderBy('distance')
            ->where(['online'=>'Y','is_connected'=>'Y','status'=>'Y','user_type'=>2])
            ->first();

        
            $result['is_job'] =  'Y';
            $result['status'] =  $order->status;
            $result["notification_type"] =  'request';
            
            $result['order_info']['cust_name'] = $order->user->full_name;
            $result['order_info']['cust_image'] = $order->user->profile_image;
            $result['order_info']['cust_address'] = $order->pickup_address;
            $result['order_info']['cust_lat'] = $order->pickup_latitude;
            $result['order_info']['cust_lng'] = $order->pickup_longitude;
            $result['order_info']['storage_address'] = $order->dropoff_address;
            $result['order_info']['storage_lat'] = $order->dropoff_latitude;
            $result['order_info']['storeage_lng'] = $order->dropoff_longitude;

            $support_call = Setting::find(7);
            $result['order_info']['support_call'] = isset($support_call) ? $support_call->value : '';

            //$result['order_info']["notification_type"] =  'request';


            $result['order_info']['id'] = $order->id;
            $result['order_info']['order_id'] = $order->id;
            $result['order_info']['bagage'] = $order->bagage;
            $result['order_info']['date'] = $order->date;
            $result['order_info']['time'] = $order->time;
            $result['order_info']['status'] = $order->status;
            $result['order_info']['order_type'] = $order->order_type;
            $result['order_info']['isInvalid'] = $order->isInvalid;
            $result['order_info']['amount'] = $order->price;
            $result['order_info']['address'] = $order->pickup_address;
            $result['order_info']['latitude'] = $order->pickup_latitude;
            $result['order_info']['longitude'] = $order->pickup_longitude;
            $result['order_info']['qr_image'] = $order->qr_image;


        $devices_object = DB::table('oauth_access_tokens')->where(['user_id'=>isset($porter->id)?$porter->id:null])->where('device_id','!=',null)->get();

        if(!empty($devices_object)){
            
            // dd($devices_object);

           $device_ids = last(array_pluck($devices_object,'device_id'));
        //   retrun $device_ids;
          //Now send the notifications
          CustomFunctions::pushNotification($device_ids,$result);

        }

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
            $result['order_list'][$i]['is_pony_service'] = $order->is_pony_service;
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
            $result['pending_bagage'] = $order->pending_bagage;
            $result['date'] = $order->date;
            $result['time'] = $order->time;
            $result['status'] = $status[$order->status];
            $result['isInvalid'] = $order->isInvalid;
            $result['amount'] = $order->price;
            $result['address'] = $order->pickup_address;
            $result['latitude'] = $order->pickup_latitude;
            $result['longitude'] = $order->pickup_longitude;

            
            $result['is_pony_service'] = $order->is_pony_service;


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
        $distance_price = 0;


        if($distance <= 2 && $distance >= 7){
         $zero_to_two = Setting::find(3);
         $distance_price += $distance * $zero_to_two->value;
        }
        else if ($distance <= 4 && $distance >= 7){
            $two_to_four = Setting::find(4);
            $distance_price += $distance * $two_to_four->value;
        }
        else if ($distance <= 4 && $distance >= 7){
            $four_to_six = Setting::find(5);
            $distance_price += $distance * $four_to_six->value;
        }
        else {
            $above_seven = Setting::find(6);
            $distance_price += $distance * $above_seven->value;
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
        $result["porter_id"] =  -1;


      //CustomFunctions::calculate_distance(22.9936,72.4987,22.7788,73.6143);

        //Lets find the porter

       $porter = User::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$request->latitude, $request->longitude, $request->latitude])
            ->having('distance', '<', 500)
            ->orderBy('distance')
            ->where(['online'=>'Y','is_connected'=>'Y','status'=>'Y','user_type'=>2])
            ->first();

        // $porter = User::where(['online'=>'Y','is_connected'=>'Y'])->first();

        if($porter){
            $result["is_porter_available"] =  'Y';
            $result["porter_latitude"]     =  $porter->latitude;
            $result["porter_longitude"]    =  $porter->longitude;
            $result["porter_id"] =  $porter->id;
        }

        $result["order_id"]  =  '';
        $result["bagage"]    =  $request->bagage;
        $result["amount"]    =  $amount; 
        $result["token"]    =  Braintree_ClientToken::generate();

        return $this->SuccessResponse($result,$message='Success');
    }


    /**
     * PonyCost Calculation
     *
     * @param  [string] bagage
     * @param  [string] date
     * @param  [string] time
     * @param  [string] isInvalid
     * @param  [string] pickup_latitude
     * @param  [string] pickup_longitude
     * @param  [string] pickup_address
     * @param  [string] dropoff_latitude
     * @param  [string] dropoff_longitude
     * @param  [string] dropoff_address
     * @return [string] message
     */

    public function PonyCost(Request $request){

        $request->validate([
            'bagage' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'isInvalid' => 'required|string',
            'pickup_latitude' => 'required|string',
            'pickup_longitude' => 'required|string',
            'pickup_address' => 'required|string',
            'dropoff_latitude' => 'required|string',
            'dropoff_longitude' => 'required|string',
            'dropoff_address' => 'required|string'
        ]);


        $distance = CustomFunctions::calculate_distance($request->pickup_latitude,$request->pickup_longitude,$request->dropoff_latitude,$request->dropoff_longitude);

        $per_lagage_charge = Setting::find(1);
        $per_day_charge = Setting::find(2);
        $distance_price = 0;


        if($distance <= 2 && $distance >= 7){
         $zero_to_two = Setting::find(3);
         $distance_price += $distance * $zero_to_two->value;
        }
        else if ($distance <= 4 && $distance >= 7){
            $two_to_four = Setting::find(4);
            $distance_price += $distance * $two_to_four->value;
        }
        else if ($distance <= 4 && $distance >= 7){
            $four_to_six = Setting::find(5);
            $distance_price += $distance * $four_to_six->value;
        }
        else {
            $above_seven = Setting::find(6);
            $distance_price += $distance * $above_seven->value;
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

        if(!empty($request->pickup_address))
             $pickup_address = $request->pickup_address;

        if(!empty($request->pickup_latitude))
             $pickup_latitude = $request->pickup_latitude;

        if(!empty($request->pickup_longitude))
             $pickup_longitude = $request->pickup_longitude;

        $result = [];

        $result["is_porter_available"] =  'Y';
        $result["porter_latitude"]     =  '';
        $result["porter_longitude"]    =  '';
        $result["porter_id"] =  -1;


      //CustomFunctions::calculate_distance(22.9936,72.4987,22.7788,73.6143);

        //Lets find the porter

       $porter = User::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$request->pickup_latitude, $request->pickup_longitude, $request->pickup_latitude])
            ->having('distance', '<', 500)
            ->orderBy('distance')
            ->where(['online'=>'Y','is_connected'=>'Y','status'=>'Y','user_type'=>2])
            ->first();

        // $porter = User::where(['online'=>'Y','is_connected'=>'Y'])->first();

        if($porter){
            $result["is_porter_available"] =  'Y';
            $result["porter_latitude"]     =  $porter->latitude;
            $result["porter_longitude"]    =  $porter->longitude;
            $result["porter_id"] =  $porter->id;
        }

        $result["order_id"]  =  '';
        $result["bagage"]    =  $request->bagage;
        $result["amount"]    =  $amount; 
        $result["token"]    =  Braintree_ClientToken::generate();

        return $this->SuccessResponse($result,$message='Success');
    }



    public function CancelOrder(Request $request){

         $request->validate([
            'order_id' => 'required|string',      
            // 'cancel_reason' => 'required|string',      
        ]);

        $user = $request->user();

        $order = Order::where(['user_id'=>$user->id,'id'=>$request->order_id])
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