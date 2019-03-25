<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\VerifyEmail;
use CustomFunctions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DriversController extends Controller
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

      public function index(Request $request)
      {

        $drivers = User::where(['user_type'=>2])->get();
        
        $params = [
            'title' => 'Drivers Listing',
            'drivers' => $drivers,
        ];

        return view('admin.drivers.drivers_list')->with($params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = [
            'title' => 'Create Driver',
            'driver'=> new User,
        ];

        return view('admin.drivers.driver_create')->with($params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'full_name' => 'required|string',
            'mobile_number' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|unique:users',
        ]);

        $driver = new User;

        if(!empty($request->input('full_name')))
            $driver->full_name = $request->input('full_name');

        if(!empty($request->input('email')))
            $driver->email = $request->input('email');

        // if(!empty($request->input('country_code')))
            $driver->country_code = '+39';

        if(!empty($request->input('mobile_number')))
            $driver->mobile_number = $request->input('mobile_number');

        if(!empty($request->input('password')))
            $driver->password = bcrypt($request->input('password'));

        if(!empty($request->input('full_name')))
            $driver->full_name = $request->input('full_name');

        $driver->verify_email_token = User::generateVerificationCode();

        $driver->email_verification_token_timeout = strtotime('+1 hour');

        $driver->user_type = '2';

        $driver->save();


        try{
        $driver->notify(new VerifyEmail);

        }catch(Exception $e){

        }
        //@todo send sms

        return redirect()->route('drivers.index')->with('success', "The driver <strong>$driver->full_name</strong> has successfully been created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $driver = User::findOrFail($id);

            $params = [
                'title' => 'Delete Driver',
                'driver' => $driver,
            ];

            return view('admin.drivers.driver_delete')->with($params);
        }
        catch (ModelNotFoundException $ex) 
        {
            if ($ex instanceof ModelNotFoundException)
            {
                return response()->view('errors.'.'404');
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try
        {
            $driver = User::findOrFail($id);

            $params = [
                'title' => 'Edit Driver',
                'driver' => $driver,
            ];

            return view('admin.drivers.driver_edit')->with($params);
        }
        catch (ModelNotFoundException $ex) 
        {
            if ($ex instanceof ModelNotFoundException)
            {
                return response()->view('errors.'.'404');
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try
        {
            $this->validate($request, [
                'full_name' => 'required|string',
                // 'mobile' => 'required|string',
                'email' => 'required|unique:users,email,'.$id,

            ]);

            $driver = User::findOrFail($id);

            $driver->full_name = $request->input('full_name');
            // $driver->contact_number = $request->input('contact_number');
            $driver->email = $request->input('email');

            // $category->image = $request->input('image');

            $driver->save();

            return redirect()->route('drivers.index')->with('success', "The driver <strong>$driver->full_name</strong> has successfully been updated.");
        }
        catch (ModelNotFoundException $ex) 
        {
            if ($ex instanceof ModelNotFoundException)
            {
                return response()->view('errors.'.'404');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $driver = User::findOrFail($id);

            $driver->delete();

            return redirect()->route('drivers.index')->with('success', "The driver <strong>$driver->full_name</strong> has successfully been archived.");
        }
        catch (ModelNotFoundException $ex) 
        {
            if ($ex instanceof ModelNotFoundException)
            {
                return response()->view('errors.'.'404');
            }
        }
    }

   public function active(Request $request){

        if($request->input('id',null)){
            $model = $this->findModel($request->input('id'));
            $model->status = $request->input('status');
            $model->save();

            $result['message'] = 'Action perform successfully';
            $result['status'] = $model->status;
            return json_encode($result);
            
        }
        $result['message'] = 'Action could not perform';
        $result['status'] = 0;
        return json_encode($result);
    }



    protected function findModel($id)
    {
        try
        {
            $model = User::findOrFail($id);
            return $model;
        }
        catch (ModelNotFoundException $ex) 
        {
            if ($ex instanceof ModelNotFoundException)
            {
                return response()->view('errors.'.'404');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile($id)
    {

        try{
            $driver = User::findOrFail($id);
            $orders = Order::where(['driver_id'=>$id])->orderBy('id', 'DESC')->get();
            $params = [
                'title' => 'Driver Detail',
                'driver' => $driver,
                'orders'=>$orders
            ];

            return view('admin.drivers.driver_profile')->with($params);
        }
        catch (ModelNotFoundException $ex) 
        {
            if ($ex instanceof ModelNotFoundException)
            {
                return response()->view('errors.'.'404');
            }
        }
    }
}