<?php

namespace App\Http\Controllers\API\Cms;

use App\Http\Controllers\API\Base\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\Report;
use Illuminate\Http\Request;

class CmsController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        $cms['about_us'] = url('/').'/about?type=0';
        $cms['terms_and_condition'] = url('/').'/terms?type=0';
        $cms['privacy'] = url('/').'/privacy?type=0?type=0';
        return $this->SuccessResponse($cms,$message='Success');
    }

    public function DriverCms()
    {
        $cms['about_us'] = url('/').'/about?type=1';
        $cms['terms_and_condition'] = url('/').'/terms?type=1';
        $cms['privacy'] = url('/').'/privacy?type=1';
        return $this->SuccessResponse($cms,$message='Success');
    }

}
