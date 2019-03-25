<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Order;
use App\Models\User;
use App\Notifications\VerifyEmail;
use CustomFunctions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class OrdersController extends Controller
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

        $orders = Order::with('user')->orderBy('id', 'ASC')->get();

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

        
        $params = [
            'title' => 'Requests Listing',
            'orders' => $orders,
            'status'=>$status
        ];
        
        return view('admin.orders.orders_list')->with($params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = [
            'title' => 'Create order',
            'order'=> new User,
        ];

        return view('admin.orders.order_create')->with($params);
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

        $order = new User;

        if(!empty($request->input('full_name')))
            $order->full_name = $request->input('full_name');

        if(!empty($request->input('email')))
            $order->email = $request->input('email');

        // if(!empty($request->input('country_code')))
            $order->country_code = '+39';

        if(!empty($request->input('mobile_number')))
            $order->mobile_number = $request->input('mobile_number');

        if(!empty($request->input('password')))
            $order->password = bcrypt($request->input('password'));

        if(!empty($request->input('full_name')))
            $order->full_name = $request->input('full_name');

        $order->verify_email_token = User::generateVerificationCode();

        $order->email_verification_token_timeout = strtotime('+1 hour');

        $order->user_type = '2';

        $order->save();


        try{
        $order->notify(new VerifyEmail);

        }catch(Exception $e){

        }
        //@todo send sms

        return redirect()->route('orders.index')->with('success', "The order <strong>$order->full_name</strong> has successfully been created.");
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
            $order = Order::with('user')->findOrFail($id);

            $params = [
                'title' => 'Delete request',
                'order' => $order,
            ];

            return view('admin.orders.order_delete')->with($params);
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
            $order = User::findOrFail($id);

            $params = [
                'title' => 'Edit order',
                'order' => $order,
            ];

            return view('admin.orders.order_edit')->with($params);
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

            $order = User::findOrFail($id);

            $order->full_name = $request->input('full_name');
            // $order->contact_number = $request->input('contact_number');
            $order->email = $request->input('email');

            // $category->image = $request->input('image');

            $order->save();

            return redirect()->route('orders.index')->with('success', "The order <strong>$order->full_name</strong> has successfully been updated.");
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
            $order = Order::findOrFail($id);

            $order->delete();

            return redirect()->route('orders.index')->with('success', "The order <strong>$order->full_name</strong> has successfully been archived.");
        }
        catch (ModelNotFoundException $ex) 
        {
            if ($ex instanceof ModelNotFoundException)
            {
                return response()->view('errors.'.'404');
            }
        }
    }

   public function status($id){

            $model = $this->findModel($id);
            $model->is_payment_received = 'Y';
            $model->save();

            return redirect()->route('orders.profile',['id'=>$id])->with('success', "The order <strong>Payment Status</strong> has successfully been change.");
    }


    protected function findModel($id)
    {
        try
        {
            $model = Order::findOrFail($id);
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
            $order = Order::with(['user'])->findOrFail($id);

            $imagesAndDriver = Image::with(['driver'])->where(['order_id'=>$order->id])
            // ->groupBy('driver_id')
            ->get();

            $imagesAndDriver2 = $imagesAndDriver->groupBy('driver_id');

            // dd($imagesAndDriver);

            $params = [
                'title' => 'Request Detail',
                'order' => $order,
                'imagesAndDriver'=>$imagesAndDriver,
                'imagesAndDriver2'=>$imagesAndDriver2
            ];

            return view('admin.orders.order_profile')->with($params);
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