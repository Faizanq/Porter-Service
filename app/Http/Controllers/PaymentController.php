<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\UserPackage;
use Illuminate\Http\Request;
use Paystack;

class PaymentController extends Controller
{

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        // dd(Paystack::getAuthorizationUrl()->redirectNow());
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        // dd($paymentDetails);
        
        if(!empty($paymentDetails['data'])){
            if($paymentDetails['data']['status'] == 'success'){
                $package = Package::findOrFail($paymentDetails['data']['metadata']['package_id']);
                if($package){
                    $user_package = UserPackage::create([
                        'package_id'=>$package->id,
                        'user_id'=>$paymentDetails['data']['metadata']['user_id'],
                        'package_name'=>$package->name,
                        'price'=>$package->price,
                        'received_keys'=>$package->key,
                        ]);

                    $transaction = Transaction::create([
                        'package_id'=>$package->id,
                        'user_id'=>$paymentDetails['data']['metadata']['user_id'],
                        'package_name'=>$package->name,
                        'price'=>$package->price,
                        'reference'=>$paymentDetails['data']['reference'],
                        ]);

                    $response['error'] = new Package;
                    $response['data']['message'] = $paymentDetails['data']['gateway_response'];
                    $response['data']['is_successful'] = 1;    
                    return response()->json($response,$code=200);
                }
                $response['error'] = new Package;
                    $response['data']['message'] = $paymentDetails['data']['gateway_response'];
                    $response['data']['is_successful'] = 0;    
                    return response()->json($response,$code=200);
            }
        }
        $response['error'] = new Package;
        $response['data']['message'] = $paymentDetails['data']['gateway_response'];
        $response['data']['is_successful'] = 0;    
        return response()->json($response,$code=200);
    }
}