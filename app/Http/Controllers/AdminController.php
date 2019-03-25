<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Apply;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Session;
use Redirect;
class AdminController extends Controller
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

    //  public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         if(Session::get('user_id') == NULL)
    //         {
    //             return Redirect::to('admin/login');
    //         }else{
    //             return $next($request);
    //         }
    // });
    // }

    public function index(){
    	
        $params['title'] = 'Dashboard';
        // $params['applicant'] = User::where('user_type',User::USER)->count();
        // $params['employer'] = User::where('user_type',User::EMPLOYER)->count();
        // $params['jobs'] = Job::count();
        // $params['applied'] = Apply::count();


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
