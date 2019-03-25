<?php
namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\API\Base\ApiController;
use App\Models\Image;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\MarkdownNotification;
use Carbon\Carbon;
use CustomFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Notification;

class DriverController extends ApiController
{

    // (1)Accept/start (2)Pick up / Arrived  / (3)Delivered
    //(4) Cancel

    public function dashboard(Request $request){

        $user = $request->user();

        $count = OrderStatus::where([
                        'driver_id'=>$user->id
                        ])
                        ->whereIn('status', ['5','7'])
                        ->get();

        $result['total_completed_orders']= count($count->where('status',5));
        $result['canceled_order'] = count($count->where('status',7));
        $result['is_job'] =  'N';
        $result['status'] =  '0';
        
                        
        $notcanceled_order =  OrderStatus::where([
                        'driver_id'=>$user->id
                        ])
                        ->whereIn('status', [1,2,3,4])
                        // ->whereNotIn('status',[5,6,7])
                        ->groupBy('order_id')
                        ->orderBy('id', 'DESC')->get();
                        
        $canceled_order =  OrderStatus::where([
                        'driver_id'=>$user->id
                        ])
                        // ->whereIn('status', [1,2,3,4])
                        ->whereIn('status',[5,6,7])
                        ->groupBy('order_id')
                        ->orderBy('id', 'DESC')->get();
        
        $array1 = $notcanceled_order->pluck('order_id')->toArray();
        $array2 = $canceled_order->pluck('order_id')->toArray();
        
        $final = array_merge(array_diff($array1, $array2), array_diff($array2, $array1));
        
        // dd($final,$array1,$array2);
               
        if($final){
            
            $last_order =  OrderStatus::where([
                        'driver_id'=>$user->id
                        ])
                        ->whereIn('order_id',$final)
                        ->orderBy('id', 'DESC')->first();
            // dd($last_order);

         if($last_order && $last_order->order_id){
            $result['is_job'] =  'Y';
            $result['status'] =  $last_order->status;
            $result['job_data']['id'] =  $last_order->order_id;
            $result['job_data']['status'] = $last_order->status;
         }
        }else{
            $result['job_data'] = json_decode('{}');
        }
            
        // $order = Order::with('user')->where(['id'=>$last_order->order_id])
        // ->first();

        // if($order){
        //     $result['is_job'] =  'Y';
        //     $result['status'] =  $order->status;
            
        //     $result['job_data']['cust_name'] = $order->user->full_name;
        //     $result['job_data']['cust_image'] = $order->user->profile_image;
        //     $result['job_data']['cust_address'] = $order->pickup_address;
        //     $result['job_data']['cust_lat'] = $order->pickup_latitude;
        //     $result['job_data']['cust_lng'] = $order->pickup_longitude;
        //     $result['job_data']['storage_address'] = $order->dropoff_address;
        //     $result['job_data']['storage_lat'] = $order->dropoff_latitude;
        //     $result['job_data']['storeage_lng'] = $order->dropoff_longitude;
        //     $result['job_data']['support_call'] = '+34 1234456776';


        //     $result['job_data']['id'] = $order->id;
        //     $result['job_data']['order_id'] = $order->id;
        //     $result['job_data']['bagage'] = $order->bagage;
        //     $result['job_data']['date'] = $order->date;
        //     $result['job_data']['time'] = $order->time;
        //     $result['job_data']['status'] = $order->status;
        //     $result['job_data']['order_type'] = $order->order_type;
        //     $result['job_data']['isInvalid'] = $order->isInvalid;
        //     $result['job_data']['amount'] = $order->price;
        //     $result['job_data']['address'] = $order->pickup_address;
        //     $result['job_data']['latitude'] = $order->pickup_latitude;
        //     $result['job_data']['longitude'] = $order->pickup_longitude;
        //     $result['job_data']['qr_image'] = $order->qr_image;
        // }
        return $this->SuccessResponse($dashbord_data=$result,$message='success');
    }



    /**
     * Edit profile
     *
     * @return response
     */
    public function EditProfile(Request $request){
        
        $request->validate([
            'email' => 'required|string',
        ]);

        $user = $request->user();

        if(!empty($request->email))
            $user->email = $request->email;

        if(!empty($request->profile_image)){

            $imageName = time().'.'.$request->profile_image->getClientOriginalExtension();

            $request->profile_image->move(public_path('img'), $imageName);

            $user->profile_image = $imageName;

        }

        $user->save();

        //@todo send OTP to mobile number or mail
        // try{
        //     $user->notify(new SendOtp);
        // }catch(Exception $e){

        // }

        $tokenArray = [
        'access_token' => '',
        'token_type' => 'Bearer',
        'expires_at' => ''
        ];

        return $this->LoginResponse($user,$tokenArray,$message='Profile edited successfully');


        // return $this->SuccessResponse($user,$message='Profile edited successfully');
    }


    public function onlineOffline(Request $request){

         $request->validate([
            'is_online' => 'required|string',      
        ]);

        $user = $request->user();

        $user->online = $request->is_online;
        $user->save();

        return $this->SuccessResponse([],$message='success');
    }


    public function CancelOrder(Request $request){

         $request->validate([
            'order_id' => 'required|string',      
            'cancel_reason' => 'required|string',      
        ]);

        $user = $request->user();

        $order = Order::where(['driver_id'=>$user->id,'id'=>$request->order_id])
        ->first();

        if(isset($request->latitude) && isset($request->longitude)){
            $result['latitude'] = $request->latitude;
            $result['longitude'] = $request->longitude;


            $result = json_encode($result);
            $order->sender_data = $result;
        }

        if(!$order)
            return $this->ErrorResponse($message='No such order found');

        $order->status = '7';
        $order->cancelation_message = $request->cancel_reason;
        $order->save();

        //Lets create the status entry
        OrderStatus::create([
                        'driver_id'=>$user->id,
                        'order_id'=>$order->id,
                        'status'=>$order->status,

                    ]);


        //Lets Notify the User

        return $this->SuccessResponse([],$message='success');
    }


    public function UpdateOrder(Request $request){

         $request->validate([
            'order_id' => 'required|string',      
            'status' => 'required|string',      
        ]);

        $user = $request->user();

        $order = Order::where(['driver_id'=>$user->id,'id'=>$request->order_id])
        ->first();
        
        if(!$order){
         $order = Order::where(['id'=>$request->order_id])
            ->first();
            $order->driver_id = $user->id;
        }

        if(!$order)
            return $this->ErrorResponse($message='No such order found');

        $order->status = $request->status;
        $order->save();

        //Lets create the status entry
        OrderStatus::create([
                        'driver_id'=>$user->id,
                        'order_id'=>$order->id,
                        'status'=>$order->status,

                    ]);

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

            $result['status_code'] =  $order->status;
            $result['status']      =  $status[$order->status];
            $result["notification_type"] =  $order->status;
            $result["order_id"]    =  $order->id;
            $result["message"]     =  'Order status updated';

            $result['order_info']['order_id'] = $order->id;
            $result['order_info']['id'] = $order->id;
            $result['order_info']['order_id'] = $order->id;
            $result['order_info']['bagage']   = $order->bagage;
            $result['order_info']['date']     = $order->date;
            $result['order_info']['time']     = $order->time;
            $result['order_info']['status']   = $order->status;


         $status_messages = [
            2=>'Your order has been started',
            3=>'Driver arrived at your place',
            4=>'Driver picked up your baggage',
            5=>'Your order has been delivered successfully',
        ];

    if(!empty($status_messages[$request->status])){
        $result["message"] = $status_messages[$request->status];
        //Lets notify the user about order update
        $devices_object = DB::table('oauth_access_tokens')->where(['user_id'=>isset($order->user_id)?$order->user_id:null])->where('device_id','!=',null)->get();

        if(!empty($devices_object)){            

          $device_ids = last(array_pluck($devices_object,'device_id'));
          
          // return $device_ids;
          //Now send the notifications
          CustomFunctions::pushNotification($device_ids,$result);

          CustomFunctions::StoreNotification($from=$user->id,$to=$order->user_id,$to_user='U',$type=$result["notification_type"],$result);

        }
    }

        return $this->SuccessResponse([],$message='success');
    }

 

    /**
     * Return Driver Order list
     * @return [Object] result
     */

    public function List(Request $request){

        $offset = isset($request->start) && $request->start !=null ? $request->start:0;
        $limit = isset($request->limit) && $request->limit !=null ? $request->limit+1:11;

        $user = $request->user();

        $orders =  Order::with(['user','laststatus'=>function($query){
            return $query->orderBy('id','desc');
        }])->where(['driver_id'=>$user->id]);


        if(!empty($request->status))
        $orders =  $orders->where(['status'=>$request->status]);



        $orders = $orders->offset($offset)->take($limit)->orderBy('id', 'DESC')->get();

        // dd($orders);


        $result['order_list'] = [];
        $i = 0;
        foreach ($orders as $key => $order) {
            $result['order_list'][$i]['id'] = $order->id;
            $result['order_list'][$i]['order_id'] = $order->id;
            $result['order_list'][$i]['order_status'] = isset($order->laststatus)?$order->laststatus->status:$order->status;
            $result['order_list'][$i]['price'] = $order->price;
            $result['order_list'][$i]['date'] = $order->date;
            $result['order_list'][$i]['starting_address'] = $order->pickup_address;
            $result['order_list'][$i]['starting_latitude'] = $order->pickup_latitude;
            $result['order_list'][$i]['starting_longitude'] = $order->pickup_longitude;

            $result['order_list'][$i]['ending_address'] = $order->dropoff_address;
            $result['order_list'][$i]['ending_latitude'] = $order->dropoff_latitude;
            $result['order_list'][$i]['ending_longitude'] = $order->dropoff_longitude;

             $result['order_list'][$i]['is_pony_service'] = $order->is_pony_service;

            $result['order_list'][$i]['order_type'] = $order->order_type;
            $result['order_list'][$i]['user_name'] = $order->user ? $order->user->full_name:'';
            $result['order_list'][$i]['user_state'] = '';
            $result['order_list'][$i]['user_image'] = $order->user ? $order->user->profile_image:'';
            $result['order_list'][$i]['estimated_time'] = $order->time;
            $result['order_list'][$i]['estimated_distanc'] = $order->distance;

        $i++;
        }

        $is_last = 'Y';

        $message = 'No order found';
        if($i)
            $message = 'order list';

        if($i >= $limit){
            unset($result['order_list'][$i-1]);
            $is_last = 'N';

        }
        return $this->SuccessList($result,$message,200,$is_last);
    }



    public function Detail(Request $request)
    {


        $request->validate([
            'order_id' => 'required|string',
        ]);


        $user = $request->user();

        $order = Order::with('user')->where(['id'=>$request->order_id])->first();

        $order_status = OrderStatus::where(['order_id'=>$order->id])->orderBy('id','DESC')->first();

        
        if($order == null)
            return $this->ErrorResponse('No such order found');

        $result = [];

            $result['id'] = $order->id;
            $result['order_id'] = $order->id;
            $result['no_of_bag'] = $order->bagage;
            $result['pending_bagage'] = $order->pending_bagage;
            $result['date'] = $order->date;
            $result['time'] = $order->time;
            $result['order_status'] = isset($order_status)?$order_status->status:$order->status;
            $result['order_type'] = $order->order_type;
            $result['isInvalid'] = $order->isInvalid;
            $result['user_state'] = '';
            $result['price'] = $order->price;
            $result['starting_address'] = $order->pickup_address;
            $result['starting_latitude'] = $order->pickup_latitude;
            $result['starting_longitude'] = $order->pickup_longitude;
            $result['ending_address'] = $order->dropoff_address;
            $result['ending_latitude'] = $order->dropoff_latitude;
            $result['ending_longitude'] = $order->dropoff_longitude;

            $result['is_pony_service'] = $order->is_pony_service;


            $result['user_name'] = $order->user ? $order->user->full_name:'';
            $result['user_state'] = '';
            $result['user_image'] = $order->user ? $order->user->profile_image:'';
            $result['estimated_time'] = $order->time;
            $result['estimated_distanc'] = $order->distance;
            $result['qr_image'] = $order->qr_image;

            $support_call = Setting::find(7);
            $result['support_call'] = isset($support_call) ? $support_call->value : '';

            // $result['support_call'] = '+34 1234456776';
            
        $message = 'order detail';

        return $this->SuccessResponse($result,$message,200);

    }


    /**
     * Return Driver Order list
     * @return [Object] result
     */

    public function PendingOrders(Request $request){

        $offset = isset($request->start) && $request->start !=null ? $request->start:0;
        $limit = isset($request->limit) && $request->limit !=null ? $request->limit+1:11;

        $user = $request->user();

        // $orders =  Order::with('user')->whereNotIn('status',[1,2,3,4,5,6,7]);

        $orders =  Order::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( pickup_latitude ) ) * cos( radians( pickup_longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( pickup_latitude ) ) ) ) AS distance', [$user->latitude, $user->longitude, $user->latitude])
            ->having('distance', '<', 500)
            ->orderBy('distance')->with('user')->whereNotIn('status',[1,2,3,4,5,6,7]);

        if(!empty($request->status))
        $orders =  $orders->where(['status'=>$request->status]);
                          // ->whereNotIn('status',[1,2,3,4,5,6,7]);

        $orders = $orders->offset($offset)->take($limit)->orderBy('id', 'DESC')->get();

        $result['order_list'] = [];
        $i = 0;

        if($orders == []){

        }
        foreach ($orders as $key => $order) {

            $result['order_list'][$i]['id'] = $order->id;
            $result['order_list'][$i]['order_id'] = $order->id;
            $result['order_list'][$i]['order_status'] = $order->status;
            $result['order_list'][$i]['price'] = $order->price;
            $result['order_list'][$i]['date'] = $order->date;

            $result['order_list'][$i]['no_of_bagage'] = $order->bagage;
            $result['order_list'][$i]['pending_bagage'] = $order->pending_bagage;

            $result['order_list'][$i]['starting_address'] = $order->pickup_address;
            $result['order_list'][$i]['starting_latitude'] = $order->pickup_latitude;
            $result['order_list'][$i]['starting_longitude'] = $order->pickup_longitude;

            $result['order_list'][$i]['ending_address'] = $order->dropoff_address;
            $result['order_list'][$i]['ending_latitude'] = $order->dropoff_latitude;
            $result['order_list'][$i]['ending_longitude'] = $order->dropoff_longitude;

             $result['order_list'][$i]['is_pony_service'] = $order->is_pony_service;

            $result['order_list'][$i]['order_type'] = $order->order_type;
            $result['order_list'][$i]['user_name'] = $order->user ? $order->user->full_name:'';
            $result['order_list'][$i]['user_state'] = '';
            $result['order_list'][$i]['user_image'] = $order->user ? $order->user->profile_image:'';
            $result['order_list'][$i]['estimated_time'] = $order->time;
            $result['order_list'][$i]['estimated_distanc'] = $order->distance;

        $i++;
        }

        $is_last = 'Y';

        $message = 'No order found';
        if($i)
            $message = 'order list';

        if($i >= $limit){
            unset($result['order_list'][$i-1]);
            $is_last = 'N';

        }
        return $this->SuccessList($result,$message,200,$is_last);
    }

    /**
    * Recieve No of Laguage
    *
    **/

    public function RecieveLaguage(Request $request){

         $request->validate([
            'order_id' => 'required|string',      
            'no_of_laguage' => 'required|string',      
        ]);

        $user = $request->user();

        $order = Order::where(['id'=>$request->order_id])
        ->first();

        if(!$order)
            return $this->ErrorResponse($message='No such order found');


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


        $order->pending_bagage -=  $request->no_of_laguage;

        if(!$order->pending_bagage)
            $order->status = 2;

        $order->save();
        //Lets create the status entry
        OrderStatus::create([
                        'driver_id'=>$user->id,
                        'order_id'=>$order->id,
                        'status'=>4,
                        'bagage'=>$request->no_of_laguage
                    ]);

            $result['status_code'] =  $order->status;
            $result['status']      =  $status[4];
            $result["notification_type"] =  $order->status;
            $result["order_id"]    =  $order->id;
            $result["message"]     =  'Order status updated';

            $result['order_info']['order_id'] = $order->id;
            $result['order_info']['id'] = $order->id;
            $result['order_info']['order_id'] = $order->id;
            $result['order_info']['bagage']   = $order->bagage;
            $result['order_info']['date']     = $order->date;
            $result['order_info']['time']     = $order->time;
            $result['order_info']['status']   = 4;


         $status_messages = [
            2=>'Your order has been started',
            3=>'Driver arrived at your place',
            4=>'Driver picked up your baggage',
            5=>'Your order has been delivered successfully',
        ];

    if(!empty($status_messages[4])){
        $result["message"] = $status_messages[4];
        //Lets notify the user about order update
        $devices_object = DB::table('oauth_access_tokens')->where(['user_id'=>isset($order->user_id)?$order->user_id:null])->where('device_id','!=',null)->get();

        if(!empty($devices_object)){            
           // dd($devices_object,$result);
           $device_ids = last(array_pluck($devices_object,'device_id'));
          // return $device_ids;
          //Now send the notifications
          CustomFunctions::pushNotification($device_ids,$result);

          CustomFunctions::StoreNotification($from=$user->id,$to=$order->user_id,$to_user='U',$type=$result["notification_type"],$result);

        }
    }

        return $this->SuccessResponse([],$message='success');
    }


   /**
    * Status Change for the order
    *
    **/

    public function ChangeStatus(Request $request){

         $request->validate([
            'order_id' => 'required|string',      
            'status' => 'required|string',      
        ]);

        $user = $request->user();

        $order = Order::where(['id'=>$request->order_id])
        ->first();
        

        if(!$order)
            return $this->ErrorResponse($message='No such order found');

        // $order->status = $request->status;
        // $order->save();

        //Lets create the status entry
        $order_statsus = OrderStatus::create([
                        'driver_id'=>$user->id,
                        'order_id'=>$order->id,
                        'status'=>$request->status,

                    ]);

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

            $result['status_code'] =  $request->status;
            $result['status']      =  $status[$request->status];
            $result["notification_type"] =  $order->status;
            $result["order_id"]    =  $order->id;
            $result["message"]     =  'Order status updated';

            $result['order_info']['order_id'] = $order->id;
            $result['order_info']['id'] = $order->id;
            $result['order_info']['order_id'] = $order->id;
            $result['order_info']['bagage']   = $order->bagage;
            $result['order_info']['date']     = $order->date;
            $result['order_info']['time']     = $order->time;
            $result['order_info']['status']   = $order->status;


         $status_messages = [
            2=>'Your order has been started',
            3=>'Driver arrived at your place',
            4=>'Driver picked up your baggage',
            5=>'Your order has been delivered successfully',
        ];

    if(!empty($status_messages[$request->status])){
        $result["message"] = $status_messages[$request->status];
        //Lets notify the user about order update
        $devices_object = DB::table('oauth_access_tokens')->where(['user_id'=>isset($order->user_id)?$order->user_id:null])->where('device_id','!=',null)->get();

        if(!empty($devices_object)){            
           // dd($devices_object,$result);
           $device_ids = last(array_pluck($devices_object,'device_id'));
          // return $device_ids;
          //Now send the notifications
          CustomFunctions::pushNotification($device_ids,$result);

          CustomFunctions::StoreNotification($from=$user->id,$to=$order->user_id,$to_user='U',$type=$result["notification_type"],$result);

        }
    }

        return $this->SuccessResponse([],$message='success');
    }

    /**
    * Status Change for the order
    *
    **/

    public function UploadImages(Request $request){

         $request->validate([
            'order_id' => 'required|string',      
            'images' => 'required|string',      
        ]);

        $user = $request->user();

        $order = Order::where(['id'=>$request->order_id])
        ->first();
        

        if(!$order)
            return $this->ErrorResponse($message='No such order found');

        foreach (json_decode($request->images) as $key => $value) {
            $image = new Image;
            $image->driver_id = $user->id;
            $image->order_id = $request->order_id;
            $image->image = $value;
            $image->save();
        }

        return $this->SuccessResponse([],$message='success');
    }


}