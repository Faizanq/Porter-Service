<?php
namespace App\Http\Controllers\API\General;
use App\Http\Controllers\API\Base\ApiController;
use App\Models\User;
use App\Notifications\ForgotPassword;
use App\Notifications\MarkdownNotification;
use App\Notifications\SendOtp;
use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use CustomFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Notification;

class GeneralController extends ApiController
{
   

     public function index()
    {
  
        
        $reasons = Reason::all();
        $i = 0;
        $result['reject_reason_list'] = [];
        
        foreach ($reasons as $key => $reason) {
            $result['reject_reason_list'][$i]['id']=$reason->id;
            $result['reject_reason_list'][$i]['reason']=$reason->reason;
            $result['reject_reason_list'][$i]['image']=$reason->image;
            $i++;
        }
        return $this->SuccessResponse($result);
    }                              
}