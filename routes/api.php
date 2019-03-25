<?php

use App\Helper\CustomFunctions;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



/*
* Cms
*/
Route::post('cms','API\Cms\CmsController@index');
Route::post('drivercms','API\Cms\CmsController@DriverCms');



/*
* Resons
*/
Route::post('reasons','API\Reason\ReasonController@index');
Route::apiResource('reasons','API\Reason\ReasonController',['only'=>['index']]);


/*
*  Auth without middleware
*/

Route::prefix('auth')->group(function () {
   
   Route::post('register','API\Auth\AuthController@Register');
   Route::post('login','API\Auth\AuthController@login');
   Route::post('driver-login','API\Auth\AuthController@driverlogin');
   Route::post('sociallogin','API\Auth\AuthController@sociallogin');
   Route::post('sendotp','API\Auth\AuthController@sendotp');

   Route::post('verifyotp','API\Auth\AuthController@verifyotp');
   Route::post('resetpassword','API\Auth\AuthController@resetpassword');

   Route::post('forgotpassword','API\Auth\AuthController@forgotpassword');
   Route::post('forgotpassworddriver','API\Auth\AuthController@forgotpassworddriver');


});

//Users Routes without middleware
   Route::prefix('user')->group(function () {

      Route::post('login','API\User\UserController@login');

  });

Route::middleware(['auth:api'])->group(function () {


	/*
	*  Auth with middleware
	*/
	Route::prefix('auth')->group(function () {
   
    Route::post('logout','API\Auth\AuthController@logout');
    Route::post('completeprofile','API\Auth\AuthController@completeprofile');
    Route::post('addeditmobile','API\Auth\AuthController@addeditmobile');
    Route::post('changepin','API\Auth\AuthController@changepin');

    
    Route::post('notificationsetting','API\Notification\NotificationController@notificationsetting');

    Route::post('notificationlist','API\Notification\NotificationController@notificationList');

	});

  //Drivers Routes
  Route::prefix('driver')->group(function () {

      Route::post('dashboard','API\Driver\DriverController@dashboard');
      Route::post('onlineoffline','API\Driver\DriverController@onlineoffline');
      Route::post('editprofile','API\Driver\DriverController@editprofile');
      Route::post('list','API\Driver\DriverController@list');
      
      Route::post('detail','API\Driver\DriverController@Detail');

      Route::post('cancel','API\Driver\DriverController@CancelOrder');
      Route::post('updatestatus','API\Driver\DriverController@UpdateOrder');


      Route::post('pendingorders','API\Driver\DriverController@PendingOrders');
      Route::post('recievelaguage','API\Driver\DriverController@RecieveLaguage');
      Route::post('changestatus','API\Driver\DriverController@ChangeStatus');
      
      Route::post('uploadimages','API\Driver\DriverController@UploadImages');




  });

  //Users Routes
   Route::prefix('user')->group(function () {

      Route::post('post','API\User\UserController@post');
      Route::post('pony','API\User\UserController@PostPony');
      Route::post('cost','API\User\UserController@cost');
      Route::post('ponycost','API\User\UserController@PonyCost');
      Route::post('cancel','API\User\UserController@CancelOrder');
      Route::post('list','API\User\UserController@list');
      Route::post('orderdetail','API\User\UserController@detail');
      Route::post('summery','API\User\UserController@summery');

  });


});



Route::get('/file',function(){

  // $name = str_random();
  // QrCode::format('png')->size(100)->generate('Make me into a QrCode!', '../public/img/qrcode/'.$name.'.png');


  // to calculate distance between two lat & lon
  dd(CustomFunctions::sendSms());

});











