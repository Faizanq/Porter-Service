<?php 

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ApiResponser
{
	protected function SuccessResponse($data,$message='Success',$code=200){
		

        $response['error'] = array(); 
        $response['data'] = $data;
        if(!empty($message))
         $response['data']['message'] = $message;
        $response['success'] = 1; 
		return response()->json($response,$code);
	}

	protected function LoginResponse($data,$token,$message='Success',$code=200){
		

        $response['error'] = array(); 

        $data = ['user_data'=>$data,'token'=>$token];

        $response['data'] = $data;
        if(!empty($message))
         $response['data']['message'] = $message;
     
        $response['success'] = 1; 
		return response()->json($response,$code);
	}

	protected function ErrorResponse($message='Error',$code=400){

		 if(!empty($message))
             $response['error'][] = $message;  
        
        $response['data'] = [];  
        $response['success'] = 0; 
        return response()->json($response, $code);
	}

	protected function SuccessList($data,$message='Success',$code=200,$is_last=0){
		

        $response['error'] = array(); 
        $response['data'] = $data;
        $response['data']['is_last'] = $is_last;
        if(!empty($message))
         $response['data']['message'] = $message;
        $response['success'] = 1; 
		return response()->json($response,$code);
	}

	protected function showAll(Collection $collection,$code=200){
		return $this->SuccessResponse($collection,'',$code);
	}

	protected function showOne(Model $model,$code=200){
		return $this->SuccessResponse($model,'',$code);
	}
}
?>