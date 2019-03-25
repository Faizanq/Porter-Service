<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apply;
use App\Models\Bookmark;
use App\Models\Job;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use CustomFunctions;
use Illuminate\Http\Request;

class JobsController extends Controller
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

        $jobs = Job::with(['user','apply']);

        if($request->input('id',null)){
            $jobs = $jobs->where(['user_id'=>$request->input('id')])->get();

        }elseif($request->input('applicant_id',null)){
            
            $applies = Apply::where(['user_id'=>$request->input('applicant_id')])->get();

            $job_ids = array_pluck($applies,'job_id');

            $jobs = $jobs->whereIn('id',$job_ids)->get();

        }elseif($request->input('bookmark_id',null)){
            
            $bookmark = Bookmark::where(['user_id'=>$request->input('bookmark_id')])->get();

            $job_ids = array_pluck($bookmark,'job_id');

            $jobs = $jobs->whereIn('id',$job_ids)->get();

        }
        else{
            $jobs = $jobs->get();
        }

        $params = [
            'title' => 'Jobs Listing',
            'jobs' => $jobs,
        ];
        
        return view('admin.jobs.jobs_list')->with($params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = [
            'title' => 'Create Job',
        ];

        return view('admin.jobs.job_create')->with($params);
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
            'name' => 'required|unique:jobs',
        ]);

        $job = Job::create([
            'name' => $request->input('name'),
            // 'image' => $request->input('image'),
        ]);

        return redirect()->route('jobs.index')->with('success', "The job <strong>$job->name</strong> has successfully been created.");
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
            $job = Job::findOrFail($id);

            $params = [
                'title' => 'Delete Job',
                'job' => $job,
            ];

            return view('admin.jobs.job_delete')->with($params);
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
            $job = Job::findOrFail($id);

            $params = [
                'title' => 'Edit Job',
                'job' => $job,
            ];

            return view('admin.jobs.job_edit')->with($params);
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
                'name' => 'required|unique:job,name,'.$id,
            ]);

            $job = Job::findOrFail($id);

            $job->name = $request->input('name');
            // $category->image = $request->input('image');

            $job->save();

            return redirect()->route('jobs.index')->with('success', "The job <strong>$job->name</strong> has successfully been updated.");
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
            $job = Job::findOrFail($id);

            $job->delete();

            return redirect()->route('jobs.index')->with('success', "The job <strong>$job->name</strong> has successfully been archived.");
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
            $model = Job::findOrFail($id);
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
    public function detail($id)
    {

        try{

            $job = Job::with(['user','category','apply','company'])->where('id',$id)->first();

            $params = [
                'title' => 'Job Detail',
                'job'=>$job,
                'salaryType'=>CustomFunctions::salaryType()
            ];

            // dd($job);

            return view('admin.jobs.job_detail')->with($params);
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