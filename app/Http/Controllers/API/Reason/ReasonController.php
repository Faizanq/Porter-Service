<?php

namespace App\Http\Controllers\API\Reason;

use App\Http\Controllers\API\Base\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        $reasons = Reason::all();
        $i = 0;
        $result['cancel_reason'] = [];
        
        foreach ($reasons as $key => $reason) {
            $result['cancel_reason'][$i]['id']=$reason->id;
            $result['cancel_reason'][$i]['reason']=$reason->reason;
            $i++;
        }
        return $this->SuccessResponse($result);
    }

}
