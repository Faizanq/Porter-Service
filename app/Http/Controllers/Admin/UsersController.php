<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\VerifyEmail;
use CustomFunctions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UsersController extends Controller
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

        $users = User::where(['user_type'=>1])->get();
        
        $params = [
            'title' => 'Users Listing',
            'users' => $users,
        ];

        // dd($users);

        return view('admin.users.users_list')->with($params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = [
            'title' => 'Create User',
            'user'=> new User,
        ];

        return view('admin.users.user_create')->with($params);
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|unique:users',
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'user_type' => User::USER,
            // 'image' => $request->input('image'),
            'verify_email_token'=>User::generateVerificationCode(),
            'email_verification_token_timeout' => strtotime('+1 hour')
        ]);

        try{
        $user->notify(new VerifyEmail);

        }catch(Exception $e){

        }
        //@todo send sms

        return redirect()->route('users.index')->with('success', "The user <strong>$user->full_name</strong> has successfully been created.");
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
            $user = User::findOrFail($id);

            $params = [
                'title' => 'Delete User',
                'user' => $user,
            ];

            return view('admin.users.user_delete')->with($params);
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
            $user = User::findOrFail($id);

            $params = [
                'title' => 'Edit User',
                'user' => $user,
            ];

            return view('admin.users.user_edit')->with($params);
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

            $user = User::findOrFail($id);

            $user->full_name = $request->input('full_name');
            // $user->mobile_number = $request->input('mobile_number');
            $user->email = $request->input('email');

            // $category->image = $request->input('image');

            $user->save();

            return redirect()->route('users.index')->with('success', "The user <strong>$user->full_name</strong> has successfully been updated.");
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
            $user = User::findOrFail($id);

            $user->delete();

            return redirect()->route('users.index')->with('success', "The user <strong>$user->full_name</strong> has successfully been archived.");
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
            $user = User::findOrFail($id);
            $orders = Order::where(['user_id'=>$id])->orderBy('id', 'DESC')->get();
            $params = [
                'title' => 'User Detail',
                'user' => $user,
                'orders'=>$orders
            ];

            return view('admin.users.user_profile')->with($params);
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