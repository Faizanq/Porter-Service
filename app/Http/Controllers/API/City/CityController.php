<?php
namespace App\Http\Controllers\API\City;
use App\Http\Controllers\API\Base\ApiController;
use App\Models\User;
use App\Models\City;
use App\Notifications\ForgotPassword;
use App\Notifications\MarkdownNotification;
use App\Notifications\SendOtp;
use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use CustomFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Notification;

class CityController extends ApiController
{
   

     public function index(Request $request)
    {
        $request->validate([
            'state_id' => 'required|string',
        ]);

        $cities = City::where(['state_id'=>$request->state_id])->get();
        $i = 0;
        $result['city_list'] = [];
        
        foreach ($cities as $key => $city) {
            $result['city_list'][$i]['id']=$city->id;
            $result['city_list'][$i]['name']=$city->name;
            // $result['city_list'][$i]['image']=$reason->image;
            $i++;
        }
        return $this->SuccessResponse($result);
    }                             
}