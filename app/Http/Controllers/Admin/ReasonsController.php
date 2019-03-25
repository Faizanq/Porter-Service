<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReasonsController extends Controller
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
        $reasons = Reason::all();

        $params = [
            'title' => 'Reasons Listing',
            'reasons' => $reasons,
        ];

        return view('admin.reasons.reasons_list')->with($params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = [
            'title' => 'Create Reason',
        ];

        return view('admin.reasons.reason_create')->with($params);
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
            'reason' => 'required',
        ]);

        $reason = Reason::create([
            'reason' => $request->input('reason'),
            // 'image' => $request->input('image'),
        ]);

        return redirect()->route('reasons.index')->with('success', "The reason <strong>$reason->reason</strong> has successfully been created.");
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
            $reason = Reason::findOrFail($id);

            $params = [
                'title' => 'Delete Reason',
                'reason' => $reason,
            ];

            return view('admin.reasons.reason_delete')->with($params);
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
            $reason = Reason::findOrFail($id);

            $params = [
                'title' => 'Edit Reason',
                'reason' => $reason,
            ];

            return view('admin.reasons.reason_edit')->with($params);
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
                'reason' => 'required|unique:reasons,reason,'.$id,
            ]);

            $reason = Reason::findOrFail($id);

            $reason->reason = $request->input('reason');
            // $category->image = $request->input('image');

            $reason->save();

            return redirect()->route('reasons.index')->with('success', "The reason <strong>$reason->reason</strong> has successfully been updated.");
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
            $reason = Reason::findOrFail($id);

            $reason->delete();

            return redirect()->route('reasons.index')->with('success', "The reason <strong>$reason->reason</strong> has successfully been archived.");
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
            $model = Reason::findOrFail($id);
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