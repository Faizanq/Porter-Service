<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\Setting;
use App\Models\State;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SettingsController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Settings = Setting::all();

        $params = [
            'title' => 'Update Settings',
            'settings'=>$Settings,
        ];

        return view('admin.settings.setting_create')->with($params);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $this->validate($request, [
                'laggage_price' => 'required|numeric',
                'day_price' => 'required|numeric',
                'below_two' => 'required|numeric',
                'below_four' => 'required|numeric',
                'below_six' => 'required|numeric',
                'above_seven' => 'required|numeric',
                'support_call' => 'required|string',
            ]);

            Setting::where('key','laggage_price')->update(['value' => $request->input('laggage_price')]);

            Setting::where('key','day_price')->update(['value' => $request->input('day_price')]);

            Setting::where('key','below_two')->update(['value' => $request->input('below_two')]);

            Setting::where('key','below_four')->update(['value' => $request->input('below_four')]);

            Setting::where('key','below_six')->update(['value' => $request->input('below_six')]);

            Setting::where('key','above_seven')->update(['value' => $request->input('above_seven')]);

            Setting::where('key','support_call')->update(['value' => $request->input('support_call')]);

            return redirect()->route('admin')->with('success', "The Settings has been successfully updated.");
        }
        catch (ModelNotFoundException $ex) 
        {
            if ($ex instanceof ModelNotFoundException)
            {
                return response()->view('errors.'.'404');
            }
        }
    }

    
    protected function findModel($id)
    {
        try
        {
            $model = Cms::findOrFail($id);
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
}