<?php
namespace App\Http\Controllers\API\State;
use App\Http\Controllers\API\Base\ApiController;
use App\Models\User;
use App\Models\State;
use App\Notifications\ForgotPassword;
use App\Notifications\MarkdownNotification;
use App\Notifications\SendOtp;
use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use CustomFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Notification;

class StateController extends ApiController
{
   

     public function index()
    {
        $states = State::all();
        $i = 0;
        $result['state_list'] = [];
        
        foreach ($states as $key => $state) {
            $result['state_list'][$i]['id']=$state->id;
            $result['state_list'][$i]['name']=$state->name;
            // $result['state_list'][$i]['image']=$reason->image;
            $i++;
        }
        return $this->SuccessResponse($result);
    }                              
}