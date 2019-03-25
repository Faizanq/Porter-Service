
<form method="POST" action="{{url('/')}}/pay" accept-charset="UTF-8" class="form-horizontal" role="form">
        <div class="row" style="margin-bottom:40px;">
          <div class="col-md-8 col-md-offset-2">
            <p>
                <div>
                   {{$title}}
                </div>
            </p>
            <input type="hidden" name="email" value="{{$email}}"> {{-- required --}}
            <input type="hidden" name="orderID" value="{{$order_id}}">
            <input type="hidden" name="amount" value="{{$amount}}"> {{-- required in kobo --}}
            <input type="hidden" name="quantity" value="{{$quantity}}">
            <input type="hidden" name="metadata" value="{{ json_encode($array = ['user_id' => $user_id,'package_id'=>$package_id]) }}" > 

            <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
            <input type="hidden" name="key" value="{{ config('paystack.secretKey') }}"> {{-- required --}}
            {{ csrf_field() }} 
            <p>
              <button class="btn btn-success btn-lg btn-block" type="submit" value="Pay Now!">
              <i class="fa fa-plus-circle fa-lg"></i> Pay Now!
              </button>
            </p>
          </div>
        </div>
</form>