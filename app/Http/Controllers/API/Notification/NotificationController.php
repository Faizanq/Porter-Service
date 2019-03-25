<?php
namespace App\Http\Controllers\API\Notification;
use App\Http\Controllers\API\Base\ApiController;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use CustomFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NotificationController extends ApiController
{
    /**
     * Notification setting for enable/disable
     *
     * @return [string] message
     */
    public function notificationSetting(Request $request)
    {
        
        $request->validate([
            'is_notify' => 'required|string',
        ]);

        $request->is_notify = ucfirst($request->is_notify);
        
        $user = $request->user();

        $validinput = ['Y'=>'1','N'=>'0'];

        if(!array_key_exists($request->is_notify, $validinput))
            return $this->ErrorResponse('Invalid input');
        
        $user->is_notify = $validinput[$request->is_notify];
        $user->save();

        return $this->SuccessResponse($data=[],$message='notification setting changes saved');
    }

    /**
     * Notification List
     *
     * @return [string] Object
     */
    public function notificationList(Request $request)
    {

        $offset = isset($request->start) && $request->start !=null ? $request->start:0;
        $limit = isset($request->limit) && $request->limit !=null ? $request->limit+1:11;

        $user = $request->user();

        $Notifications = Notification::where(['type'=>'U','notifiable_id'=>$user->id])->offset($offset)->take($limit)->orderBy('id', 'DESC')->get();

        $result['notification_list'] = [];
        $i = 0;
        foreach ($Notifications as $key => $notification) {

           $result['notification_list'][$i]['id'] = !empty($notification->id)?$notification->id:'';
           $result['notification_list'][$i]['notification_type'] = !empty($notification->notifiable_type)?$notification->notifiable_type:1;

           $result['notification_list'][$i]['timestamp'] = !empty($notification->created_at)?strtotime($notification->created_at):time();

           $result['notification_list'][$i]['message'] = json_decode($notification->data)->message;

           $i++;
        }
        $is_last = 1;

        $messgae = 'No Notifications yet';
        if($i)
            $messgae = 'Notification list';

        if($i >= $limit){
            unset($result['job_list'][$i-1]);
            $is_last = 0;

        }
        return $this->SuccessList($result,$messgae,200,$is_last);
    }
}