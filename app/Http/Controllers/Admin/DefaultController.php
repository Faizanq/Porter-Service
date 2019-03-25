<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Apply;
use App\Models\Job;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DefaultController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');

        // $this->middleware('guest', ['except' => ['logout', 'getLogout']]); 

        // $this->middleware(function ($request, $next) {
        //     if(empty($request->user())){
        //         return Redirect::to('login');
        //     }else{
        //         return $next($request);
        //     }
        // });
    }

    public function index(Request $request)
    {

        $params['title'] = 'Dashboard';
        $params['users'] = User::where('user_type',User::USER)->count();
        $params['drivers'] = User::where('user_type',User::DRIVER)->count();
        $params['orders'] = Order::count();

        return view('admin.default.index')->with($params);
    }

    public function profile(Request $request)
    {

        $params['title'] = 'Edit Profile';

        return view('admin.default.profile')->with($params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try
        {
            $this->validate($request, [
                'name' => 'required|string',
                'email' => 'required|unique:admins,email,'.$request->input('id'),

            ]);

            $admin = Admin::findOrFail($request->input('id'));

            $admin->name = $request->input('name');
            $admin->email = $request->input('email');

            // $category->image = $request->input('image');

            $admin->save();

            return redirect()->route('admin')->with('success', "Profile has been successfully updated.");
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
