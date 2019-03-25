<?php

use App\Models\Cms;
use App\Models\User;
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {

	$type = isset($_GET['type']) && $_GET['type'] != '' ? $_GET['type'] : 0;

 	$about = Cms::where(['code'=>'about','type'=>$type])->first();

    return view('terms',['terms'=>$about]);
});

Route::get('/terms', function () {

	$type = isset($_GET['type']) && $_GET['type'] != '' ? $_GET['type'] : 0;

	$terms = Cms::where(['code'=>'terms','type'=>$type])->first();

    return view('terms',['terms'=>$terms]);
});

Route::get('/privacy', function () {

	$type = isset($_GET['type']) && $_GET['type'] != '' ? $_GET['type'] : 0;

	$privacy = Cms::where(['code'=>'privacy','type'=>$type])->first();

    return view('terms',['terms'=>$privacy]);
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/admin', 'AdminController@index')->name('admin');

Route::prefix('admin')->group(function(){
	Route::get('login', 'Auth\AdminLoginController@showLoginForm')->name('admin-form');
	Route::post('login', 'Auth\AdminLoginController@login')->name('admin-login');
	Route::post('logout', 'Auth\AdminLoginController@logout')->name('admin-logout');

	/** admin reset password routes **/

	Route::post('password/email', 'Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
	Route::get('password/reset', 'Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
	Route::post('password/reset', 'Auth\AdminResetPasswordController@reset');
	Route::get('password/reset/{token}', 'Auth\AdminResetPasswordController@showLinkRequestForm')->name('admin.password.reset');


	Route::get('/profile', 
		'AdminController@profile');

	Route::get('/update', 
		'AdminController@update')->name('update');

Route::group(['namespace' => 'Admin'], function(){


    Route::post('/users/active', 
		'UsersController@active');
    Route::get('/users/profile/{id}', 
		'UsersController@profile')->name('users.profile');
	Route::resource('users', 
		'UsersController');


	Route::post('/drivers/active', 
		'DriversController@active');
    Route::get('/drivers/profile/{id}', 
		'DriversController@profile')->name('drivers.profile');
	Route::resource('drivers', 
		'DriversController');

	Route::get('/orders/status/{id}', 
		'OrdersController@status');
	Route::get('/orders/profile/{id}', 
		'OrdersController@profile')->name('orders.profile');
	Route::resource('orders', 
		'OrdersController');

    Route::post('/reasons/active', 
		'ReasonsController@active');
    Route::resource('reasons', 'ReasonsController');

    Route::post('/storages/active', 
		'StorageController@active');
    Route::resource('storages', 'StorageController');

    Route::get('/cms/active', 
		'CmsController@active');
	Route::resource('cms', 'CmsController');

	Route::resource('settings', 'SettingsController');

	// Route::resource('transactions', 'TransactionsController');

	Route::get('/clear-notification',function(){

		App\Models\User::where('admin', '=', 0)->update(['admin' => 1]);
		App\Models\Job::where('area_id', '=', NULL)->update(['area_id' => 1]);
		return redirect('admin');

	})->name('clear-notification');


    });

});


