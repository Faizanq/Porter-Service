<?php
namespace App\Http\Controllers\API\Auth;
use App\Http\Controllers\API\Base\ApiController;
use App\Models\User;
use App\Notifications\ForgotPassword;
use App\Notifications\MarkdownNotification;
use App\Notifications\SendOtp;
use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use CustomFunctions;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Notification;

class AuthController extends ApiController
{

    use SendsPasswordResetEmails;

    /**
     * Create user
     *
     * @param  [string] profile_image
     * @param  [string] full_name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] is_notification
     * @param  [string] is_terms_accept
     * @param  [string] dob
     * @param  [string] gender
     * @return [string] message
     */


    public function Register(Request $request)
    {

        $request->validate([
            'full_name' => 'required|string',
            'dob' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string',
            'is_notification' => 'required|string',
            'gender' => 'required|string',
            'is_terms_accept' => 'required|string'
        ]);

        //@todo need device_id and device_type

        //Check Existing user with email
        if(User::where(['email'=>$request->email])->first()){
        	return $this->ErrorResponse('Email already taken');
        }

        if(!empty($request->latitude))
            $data['latitude'] = $request->latitude;

        if(!empty($request->longitude))
            $data['longitude'] = $request->longitude;

        if(!empty($request->full_name))
            $data['full_name'] = $request->full_name;

        if(!empty($request->address))
            $data['address'] = $request->address;

        if(!empty($request->dob))
            $data['dob'] = $request->dob;

        if(!empty($request->is_notification))
            $data['is_notification'] = $request->is_notification;

        if(!empty($request->gender))
            $data['gender'] = $request->gender;

        if(!empty($request->is_terms_accept))
            $data['is_accepted_terms'] = $request->is_terms_accept;
        
        if(!empty($request->email))
            $data['email'] = $request->email;

        if(!empty($request->city))
            $data['city'] = $request->city;

        if(!empty($request->state))
            $data['state'] = $request->state;

        if(!empty($request->password))
            $data['password'] = bcrypt($request->password);

        $data['verify_email_token'] = User::generateVerificationCode();

        $data['user_type'] = 1;

        $data['email_verification_token_timeout'] = strtotime('+1 hour');

         if(!empty($request->profile_image)){

            $imageName = time().'.'.$request->profile_image->getClientOriginalExtension();

            $request->profile_image->move(public_path('img'), $imageName);

            $data['profile_image'] = $imageName;

        }


        $user = new User($data);
        $user->save();

        $user = User::find($user->id);

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

        //@todo send email verification link
        try{
        $user->notify(new VerifyEmail);
            // Notification::send($user,new MarkdownNotification);
        }catch(Exception $e){

        }

        return $this->LoginResponse($user,$tokenArray,'Successfully Registered');

    }
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_id' => 'required|string',
            'device_type' => 'required|string',
            'language' => 'required|string',        
        ]);

        //@todo need device_id and device_type

        // dd($request);

        $credentials = request(['email', 'password']);


        if(!Auth::attempt($credentials))
          return $this->ErrorResponse('Invalid credentilas');

        $user = $request->user();

        if(!empty($request->language)){
            $user->language = $request->language;
            $user->save();
        }


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

        return $this->LoginResponse($user,$tokenArray,'Successfully login');
    }


    /**
     * Socila Login user and create token
     *
     * @param  [string] social_id
     * @param  [string] email
     * @param  [string] social_type
     * @param  [string] user_type
     * @param  [string] last_name
     * @param  [string] first_name
     * @param  [string] device_id
     * @param  [string] device_type
     * @return [object] user 
     */
    public function socialLogin(Request $request)
    {
        $request->validate([
            'social_id' => 'required|string',
            'social_type' => 'required|string',
            'language' => 'required|string'
        ]);

        //@todo need to  store device_id and device_type
        $request->social_type = ucfirst($request->social_type);
        $request->user_type = ucfirst($request->user_type);

        if($request->social_type == User::FACEBOOK){

            $user = User::where(['social_id'=>$request->social_id])->first();
            
            if($user){

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
            
            // CustomFunctions::nullyfy($user);

            return $this->LoginResponse($user,$tokenArray,'Successfully login');
            }

        }//facebook close

        if($request->social_type == User::GOOGLE){

            $user = User::where(['google_id'=>$request->social_id])->first();
            
            if($user){

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

            // CustomFunctions::nullyfy($user);

            return $this->LoginResponse($user,$tokenArray,'Successfully login');
            }
        
        }//google close

        // if($request->social_type == User::TWITER){

        //     $user = User::where(['twiter_id'=>$request->social_id])->first();
            
        //     if($user){

        //     $tokenResult = $user->createToken('secret');

        //     $token = $tokenResult->token;        

        //     if ($request->remember_me)
        //         $token->expires_at = Carbon::now()->addWeeks(1);

        //     if (!empty($request->device_id))
        //         $token->device_id = $request->device_id;
            
        //     $token->save();

        //     $tokenArray = [
        //     'access_token' => $tokenResult->accessToken,
        //     'token_type' => 'Bearer',
        //     'expires_at' => Carbon::parse(
        //             $tokenResult->token->expires_at
        //         )->toDateTimeString()
        //     ];
            
        //     // CustomFunctions::nullyfy($user);

        //     return $this->LoginResponse($user,$tokenArray,'Successfully login');
        //     }

        // }//twiter close

        // if($request->social_type == User::LINKEDIN){

        //     $user = User::where(['linkedin_id'=>$request->social_id])->first();
            
        //     if($user){

        //     $tokenResult = $user->createToken('secret');

        //     $token = $tokenResult->token;        

        //     if ($request->remember_me)
        //         $token->expires_at = Carbon::now()->addWeeks(1);

        //     if (!empty($request->device_id))
        //         $token->device_id = $request->device_id;
            
        //     $token->save();

        //     $tokenArray = [
        //     'access_token' => $tokenResult->accessToken,
        //     'token_type' => 'Bearer',
        //     'expires_at' => Carbon::parse(
        //             $tokenResult->token->expires_at
        //         )->toDateTimeString()
        //     ];

        //     // CustomFunctions::nullyfy($user);

        //     return $this->LoginResponse($user,$tokenArray,'Successfully login');
        //     }
        
        // }//linkedin close
        
        if(!empty($request->email)){

            //lets first find user with given email
            $user = User::where(['email'=>$request->email])->first();
            //if user found then marge the social with exists email
            if($user){

                if($request->social_type == User::FACEBOOK)
                    $user['social_id'] = $request->social_id;

                if($request->social_type == User::GOOGLE)
                    $user['social_id'] = $request->social_id;

                // if($request->social_type == User::TWITER)
                //     $user['twiter_id'] = $request->social_id;

                // if($request->social_type == User::LINKEDIN)
                //     $user['linkedin_id'] = $request->social_id;

                $user['social_type'] = $request->social_type;

                $user->save();

                $tokenResult = $user->createToken('secret');

                $token = $tokenResult->token;        

                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);
                
                $token->save();

                $tokenArray = [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ];

                // CustomFunctions::nullyfy($user);

                return $this->LoginResponse($user,$tokenArray,'Successfully login');

            }else{
                //not found with this email the make new user
                goto makeNewUser;
            }
        }//close if email exists

            makeNewUser:

            $newUser = [];

            if(!empty($request->country_code))
                $newUser['country_code'] = $request->country_code;

            if(!empty($request->profile))
                $newUser['image_url'] = $request->profile;

            if(!empty($request->mobile_no))
                $newUser['mobile_number'] = $request->mobile_no;

            if(!empty($request->email))
                $newUser['email'] = $request->email;

            if(!empty($request->full_name))
                $newUser['full_name'] = $request->full_name;

            // if(!empty($request->last_name))
            //     $newUser['last_name'] = $request->last_name;

            if($request->social_type == User::FACEBOOK)
                $newUser['social_id'] = $request->social_id;

           if(!empty($request->language))
                $newUser['language'] = $request->language;

            // if($request->social_type == User::GOOGLE)
            //     $newUser['google_id'] = $request->social_id;

            // if($request->social_type == User::TWITER)
            //     $newUser['twiter_id'] = $request->social_id;

            // if($request->social_type == User::LINKEDIN)
            //     $newUser['linkedin_id'] = $request->social_id;

            // $newUser['user_type'] = $request->user_type;
            $newUser['social_type'] = $request->social_type;

            $user = new User($newUser);
            $user->save();

            $user = User::find($user->id);
           
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

            // CustomFunctions::nullyfy($user);

            return $this->LoginResponse($user,$tokenArray,'Successfully login');

    }//Social login end here
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->SuccessResponse($data=[],$message='Successfully logged out');
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }


    /**
     * Send Otp
     *
     * @return success response
     */
    public function sendOtp(Request $request){
        
        $request->validate([
            'country_code' => 'required|string',
            'mobile_no' => 'required|string',
        ]);

        $credentials = request(['country_code']);

        $credentials['mobile_number'] = $request->mobile_no;

        $user = $request->user();
        
        if(!$user){
            $user = User::where($credentials)->orderBy('id', 'desc')->first();
        }

        if($user == null){
            return $this->ErrorResponse('Invalid credentilas');
        }

        $user->otp = User::generateOtpCode();
        $user->save();

        //@todo send OTP to mobile number or mail
        try{
            $user->notify(new SendOtp);
            CustomFunctions::sendSms($user->country_code.$user->mobile_number,$user->otp);
        }catch(Exception $e){

        }

        return $this->SuccessResponse($data=[],$message='Your verification otp has been sent');
    }

    /**
     * Verifyotp
     * @param country_code
     * @param mobile_no
     * @param otp
     * @return success response
     */
    public function verifyOtp(Request $request)
    {

            $request->validate([
            'country_code' => 'required|string',
            'mobile_no' => 'required|string',
            'otp' => 'required|string'
        ]);

        $credentials = request(['country_code']);

        $credentials['mobile_number'] = $request->mobile_no;

        $user = $request->user();

        if(!$user){
            $user = User::where($credentials)->orderBy('id', 'desc')->first();
        }

        if($user == null){
            return $this->ErrorResponse('Invalid credentilas');
        }

        // if($user->is_mobile_verify == 'Y')
        //     return $this->ErrorResponse('Mobile number already verified');            

        if($user->otp != $request->otp){
            return $this->ErrorResponse('Invalid otp');
        }

        $user->otp = null;
        $user->is_mobile_verify = 'Y';
        $user->save();

        //@todo send OTP on mobile number

        // return $this->SuccessResponse($data=[],$message='Your mobile number verified successfully');

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

        return $this->LoginResponse($user,$tokenArray,$message='Your mobile number verified successfully');
    }


//     public function verifyOtp(Request $request){

//             $request->validate([
//             'country_code' => 'required|string',
//             'mobile_no' => 'required|string',
//             'otp' => 'required|string'
//         ]);

//         $credentials = request(['country_code']);

//         $credentials['mobile_number'] = $request->mobile_no;

//         $user = $request->user();

// $user = User::where($credentials)->orderBy('id', 'desc')->first();
//         if($user){
//             //$user = User::where($credentials)->orderBy('id', 'desc')->first();

// // if($user->is_mobile_verify == 'Y')
//             // return $this->ErrorResponse('Mobile number already verified');            

//         if($user->otp != $request->otp){
//             return $this->ErrorResponse('Invalid otp');
//         }

//         $user->otp = null;
//         $user->is_mobile_verify = 'Y';
//         $user->save();

//         }

//         if($user == null){
//             return $this->ErrorResponse('Invalid credentilas');
//         }

        

//         //@todo send OTP on mobile number

//         // return $this->SuccessResponse($data=[],$message='Your mobile number verified successfully');

//         $tokenResult = $user->createToken('secret');

//         $token = $tokenResult->token;  


//         if ($request->remember_me)
//             $token->expires_at = Carbon::now()->addWeeks(1);

//         if (!empty($request->device_id))
//             $token->device_id = $request->device_id;
        
//         $token->save();

//         $tokenArray = [
//         'access_token' => $tokenResult->accessToken,
//         'token_type' => 'Bearer',
//         'expires_at' => Carbon::parse(
//                 $tokenResult->token->expires_at
//             )->toDateTimeString()
//         ];

//         return $this->LoginResponse($user,$tokenArray,$message='Your mobile number verified successfully');
//     }


    /**
     * Resendotp
     *
     * @return success response
     */
    public function Resendotp(Request $request){
            dd('comming in Resendotp');
        
    }

    /**
     * Forgotpassword
     *
     * @return success response
     */
    public function Forgotpassword(Request $request){
                $request->validate([
            'email' => 'required|string',
        ]);

        $credentials = request(['email','user_type']);

        $user = User::where($credentials)->first();

        if($user == null){
            return $this->ErrorResponse('Invalid credentilas');
        }

        // $user->verify_email_token = User::generateVerificationCode();
        // $user->email_verification_token_timeout = strtotime('+1 hour');

        // $user->save();

        $response = $this->broker()->sendResetLink(
            $request->only('email'));


        //@todo send forgot password link to change new
        // try{
        // $user->notify(new ForgotPassword);
        // }catch(Exception $e){

        // }
        
        return $this->SuccessResponse($data=[],$message='Check your email for reset password link');
    }


    /**
     * Forgotpassword for driver
     *
     * @return success response
     */
    public function ForgotPasswordDriver(Request $request){
                $request->validate([
            'dial_code' => 'required|string',
            'mobile_no' => 'required|string',
        ]);

        $credentials = [];
        $credentials['country_code'] = $request->dial_code;
        $credentials['mobile_number'] = $request->mobile_no;

        $user = User::where($credentials)->first();

        if($user == null){
            return $this->ErrorResponse('Invalid credentilas');
        }

        // $user->verify_email_token = User::generateVerificationCode();
        // $user->email_verification_token_timeout = strtotime('+1 hour');

        $user->otp = User::generateOtpCode();
        $user->save();

        // $response = $this->broker()->sendResetLink(
        //     $request->only('email'));


        // @todo send forgot password link to change new
        try{
        // $user->notify(new ForgotPassword);
        CustomFunctions::sendSms($user->country_code.$user->mobile_number,$user->otp);
        }catch(Exception $e){

        }
        
        return $this->SuccessResponse($data=[],$message='Otp send to your mobile number');
    }

    /**
     * Change Pin with new over old
     *
     * @return success response
     */
    public function ChangePin(Request $request){
                $request->validate([
            'old_pin' => 'required|string',
            'new_pin' => 'required|string',
        ]);

        $user = $request->user();

        if(Hash::check($request->old_pin,$user->password)) {
            $user->password = bcrypt($request->new_pin);
            $user->save();
        } else {
            return $this->ErrorResponse('Invalid old passocde');
        }
        
        return $this->SuccessResponse($data=[],$message='Passcode change successfully');
    }


    /**
     * Changepassword
     *
     * @return success response
     */
    public function Changepassword(Request $request){
        
    }


    /**
     * Complete profile
     *
     * @param  [string] profile_image
     * @param  [string] full_name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] is_notification
     * @param  [string] is_terms_accept
     * @param  [string] dob
     * @param  [string] gender
     * @return [string] message
     */


    public function CompleteProfile(Request $request)
    {

        $request->validate([
            'full_name' => 'required|string',
            'dob' => 'required|string',
            'email' => 'required|unique:users,email,'.$request->user()->id,
            // 'password' => 'required|string',
            'is_notification' => 'required|string',
            'gender' => 'required|string',
            'is_terms_accept' => 'required|string'
        ]);

        $user = $request->user();

        //Check Existing user with email
        // if(User::where(['email'=>$request->email])->first()){
        //     return $this->ErrorResponse('Email already taken');
        // }

        if(!empty($request->latitude))
            $user->latitude = $request->latitude;

        if(!empty($request->longitude))
            $user->longitude = $request->longitude;

        if(!empty($request->full_name))
            $user->full_name = $request->full_name;

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

         if(!empty($request->profile_image)){

            $imageName = time().'.'.$request->profile_image->getClientOriginalExtension();

            $request->profile_image->move(public_path('img'), $imageName);

            $user->profile_image = $imageName;

        }

         $user->is_profile_completed = 'Y';


         $user->save();

        // dd(last(explode(' ',$request->header('Authorization'))));

         $tokenArray = [
        'access_token' => last(explode(' ',$request->header('Authorization'))),
        'token_type' => 'Bearer',
        'expires_at' => ''
        ];


        return $this->LoginResponse($user,$tokenArray,$message='Profile update successfully');

    }

    /**
     * Add/Edit Mobile number
     *
     * @return user object in response
     */
    public function addEditmobile(Request $request){
        
        $request->validate([
            'country_code' => 'required|string',
            'mobile_no' => 'required|string',
        ]);

        $user = $request->user();

        if(User::where(['mobile_number'=>$request->mobile_no])->where('id','<>',$user->id)->first()){
            return $this->ErrorResponse('Mobile number is already taken');
        }


        if(!empty($request->country_code))
            $user->country_code = $request->country_code;

        if(!empty($request->mobile_no))
            $user->mobile_number = $request->mobile_no;

        $user->otp = User::generateOtpCode();
        $user->save();

        //@todo send OTP to mobile number or mail
        try{
            $user->notify(new SendOtp);
        }catch(Exception $e){

        }

        return $this->SuccessResponse($user=[],$message='Mobile number added successfully');
    }


    /**
     * Login driver and create token
     *
     * @param  [string] country_code
     * @param  [string] mobile_no
     * @param  [boolean] pin
     * @param  [boolean] device_id
     * @param  [boolean] device_type
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */

    public function driverLogin(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string',
            'mobile_no' => 'required|string',
            'device_id' => 'required|string',
            'device_type' => 'required|string',
            'pin' => 'required|string',        
        ]);

        //@todo need device_id and device_type

        $credentials = request(['country_code']);

        $credentials['mobile_number'] = $request->mobile_no;
        $credentials['password'] = $request->pin;


        if(!Auth::attempt($credentials))
          return $this->ErrorResponse('Invalid credentilas');

        $user = $request->user();

        if(!empty($request->language)){
            $user->language = $request->language;
            $user->save();
        }

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

        return $this->LoginResponse($user,$tokenArray,'Successfully login');
    }


    /**
     * Resetpassword
     *
     * @return success response
     */
    public function Resetpassword(Request $request){
                $request->validate([
            'country_code' => 'required|string',
            'mobile_no' => 'required|string',
            'pin' => 'required|string',
        ]);

        $credentials = request(['country_code']);
        $credentials['mobile_number'] = $request->mobile_no;


        $user = User::where($credentials)->first();

        if($user == null){
            return $this->ErrorResponse('Invalid credentilas');
        }

        $user->password = bcrypt($request->pin);
        $user->save();

        //@todo send forgot password link to change new
        // try{
        // $user->notify(new ForgotPassword);
        // }catch(Exception $e){

        // }
        
        return $this->SuccessResponse($data=[],$message='success');
    }

    

                           
}