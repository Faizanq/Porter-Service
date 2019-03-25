<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class StorageController extends Controller
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
      public function index()
      {
        $storages = Storage::all();

        $params = [
            'title' => 'Storages Listing',
            'storages' => $storages,
        ];

        return view('admin.storages.storages_list')->with($params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = [
            'title' => 'Create Storage',
        ];

        return view('admin.storages.storage_create')->with($params);
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
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $storage = Storage::create([
            'address' => $request->input('address'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            // 'image' => $request->input('image'),
        ]);

        return redirect()->route('storages.index')->with('success', "The storage <strong>$storage->address</strong> has successfully been created.");
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
            $storage = Storage::findOrFail($id);

            $params = [
                'title' => 'Delete Storage',
                'storage' => $storage,
            ];

            return view('admin.storages.storage_delete')->with($params);
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
            $storage = Storage::findOrFail($id);

            $params = [
                'title' => 'Edit Storage',
                'storage' => $storage,
            ];

            return view('admin.storages.storage_edit')->with($params);
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

        // AIzaSyBT7FGQguFSd8ajZiuAt1zk4LCaM9LAbWo
        try
        {
            $this->validate($request, [
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            $storage = Storage::findOrFail($id);

            $storage->address = $request->input('address');
            $storage->latitude = $request->input('latitude');
            $storage->longitude = $request->input('longitude');

            $storage->save();

            return redirect()->route('storages.index')->with('success', "The storage <strong>$storage->address</strong> has successfully been updated.");
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
            $storage = Storage::findOrFail($id);

            $storage->delete();

            return redirect()->route('storages.index')->with('success', "The storage <strong>$storage->address</strong> has successfully been archived.");
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
            $model = Storage::findOrFail($id);
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