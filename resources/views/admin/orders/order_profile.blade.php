{{-- <link rel="stylesheet" href="/css/profile.css"> --}}
<link rel="stylesheet" href="{{asset('public/css/profile.css')}}">

@extends('admin.layouts.master')

@section('content')

<div class="row">
      <div class="col-xs-12 col-sm-9">
        
        <!-- user profile -->
        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">User info</h4>
          </div>
          @if($order->user->profile_image != null && $order->user->profile_image !='')
            @php
            $image = $order->user->profile_image;
            @endphp
            
            @else
            @php
             $image = url('img').'/'.'no_image_available.png';
            @endphp   
          @endif

          <div class="panel-body">
            <div class="profile__avatar">
              <img src="{{$image}}" alt="...">
            </div>
            <div class="profile__header">
              <h4>{{$order->user->full_name}}<small></small></h4>
              <p class="text-muted">
                @if(!empty($order->about))
                  {{$order->about}}
                @else
                  {{'No description'}}
                @endif
              </p>
              <p>
                <a href="#">{{$order->email}}</a>
              </p>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">Order info 
            <div class="text-right">
            @if($order->is_payment_received == null) 
            <a href="{{url('/admin/orders/status',['id'=>$order->id])}}" class="label bg-warning">Make Payment Received</a>
            @else
            <p class="label bg-green">Payment Received</p>
            @endif
            </div>

          </h4>
          </div>

          @if($order->qr_image != null && $order->qr_image !='')
            @php
            $image = $order->qr_image;
            @endphp
            
            @else
            @php
             $image = url('img').'/'.'no_image_available.png';
            @endphp   
          @endif

          <div class="panel-body">
            <div class="profile__avatar">
              <img src="{{$image}}" alt="...">
            </div>
            <div class="profile__header">
              <table class="table profile__table">
              <tbody>

                <tr>
                  <th><strong>
                  No of Bags
                  </strong></th>
                  <td>
                  @if(!empty($order->bagage))
                  {{$order->bagage}}
                  @else
                  {{'--'}}
                  @endif
                  </td>
                </tr>

                <tr>
                  <th><strong>
                  Distance
                  </strong></th>
                  <td>
                  @if(!empty($order->distance))
                  {{$order->distance}}
                  @else
                  {{'--'}}
                  @endif
                  </td>
                </tr>

                <tr>
                  <th><strong>
                  Source Address
                  </strong></th>
                  <td>
                  @if(!empty($order->pickup_address))
                  {{$order->pickup_address}}
                  @else
                  {{'--'}}
                  @endif
                  </td>
                </tr>

                <tr>
                  <th><strong>
                  Destination Address
                  </strong></th>
                  <td>
                  @if(!empty($order->dropoff_address))
                  {{$order->dropoff_address}}
                  @else
                  {{'--'}}
                  @endif
                  </td>
                </tr>

                <tr>
                  <th><strong>
                  Order Type
                  </strong></th>
                  <td>
                  @if(!empty($order->order_type))
                  {{$order->order_type == 'P' ? 'Picked Up' : 'Delivered'}}
                  @else
                  {{'--'}}
                  @endif
                  </td>
                </tr>

                <tr>
                  <th><strong>
                  Amount
                  </strong></th>
                  <td>
                  @if(!empty($order->price))
                  {{$order->price}}
                  @else
                  {{'--'}}
                  @endif
                  </td>
                </tr>


              </tbody>
            </table>
            </div>
          </div>
        </div>


        <!-- Driver profile -->
        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">Laguage Images</h4>
          </div>
         {{--  @if(!empty($order->driver->profile_image) != null && $order->driver->profile_image !='')
            @php
            $image = $order->driver->profile_image;
            @endphp
            
            @else
            @php
             $image = url('img').'/'.'no_image_available.png';
            @endphp   
          @endif --}}

          <div class="panel-body">
            @foreach($imagesAndDriver as $imageObject)
            <div class="profile__avatar">
              <img src="{{$imageObject->image}}" alt="...">
            </div>
            @endforeach
            <div class="profile__header">
             {{--  <h4>
                @if(!empty($imagesAndDriver[0]->driver))
                  {{$imagesAndDriver[0]->driver->full_name}}
                @else
                  {{'---'}}
                @endif

                <small></small></h4>
              <p class="text-muted">
                @if(!empty($order->driver->mobile_number))
                  {{$imagesAndDriver[0]->driver->mobile_number}}
                @else
                  {{'---'}}
                @endif
              </p> --}}
            </div>
          </div>
        </div>


        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">Drivers Info</h4>
          </div>
          @foreach($imagesAndDriver2 as $Driver)

          @if(!empty($Driver[0]->driver->profile_image) != null && $Driver[0]->driver->profile_image !='')
            @php
            $image = $Driver[0]->driver->profile_image;
            @endphp
            
            @else
            @php
             $image = url('img').'/'.'no_image_available.png';
            @endphp   
          @endif

          <div class="panel-body">
            
            <div class="profile__avatar">
              <img src="{{$image}}" alt="...">
            </div>
            
            <div class="profile__header">
              <h4>
                @if(!empty($Driver[0]->driver))
                  {{$Driver[0]->driver->full_name}}
                @else
                  {{'---'}}
                @endif

                <small></small></h4>
              <p class="text-muted">
                @if(!empty($Driver[0]->driver->mobile_number))
                  {{$Driver[0]->driver->mobile_number}}
                @else
                  {{'---'}}
                @endif
              </p>
            </div>
          </div>
          @endforeach
        </div>

      </div>
      <div class="col-xs-12 col-sm-3">
        
        
        <!-- Contact info -->
        <div class="profile__contact-info">
          <div class="profile__contact-info-item">
            <div class="profile__contact-info-icon">
              <i class="fa fa-phone"></i>
            </div>
            <div class="profile__contact-info-body">
              <h5 class="profile__contact-info-heading">Mobile number</h5>
              @if(!empty($order->user->mobile_number))
              ({{$order->user->country_code}}){{$order->user->mobile_number}}
              @else
              {{'Not provided'}}
              @endif
            </div>
          </div>
          <div class="profile__contact-info-item">
            <div class="profile__contact-info-icon">
              <i class="fa fa-envelope-square"></i>
            </div>
            <div class="profile__contact-info-body">
              <h5 class="profile__contact-info-heading">E-mail address</h5>
              @if(!empty($order->user))
              <a href="mailto:{{$order->user->email}}">{{$order->user->email}}</a>
              @endif
            </div>
          </div>
          <div class="profile__contact-info-item">
            <div class="profile__contact-info-icon">
              <i class="fa fa-map-marker"></i>
            </div>
            <div class="profile__contact-info-body">
              <h5 class="profile__contact-info-heading">Pickedup Address</h5>
              @if(!empty($order->pickup_address))
              {{$order->pickup_address}}
              @else
              {{'Not provided'}}
              @endif
            </div>
          </div>

           <div class="profile__contact-info-item">
            <div class="profile__contact-info-item">
              <div class="profile__contact-info-icon">
                <i class="fa fa-map-marker"></i>
              </div>
              <div class="profile__contact-info-body">
                <h5 class="profile__contact-info-heading">Dropoff Address</h5>
                @if(!empty($order->dropoff_address))
                {{$order->dropoff_address}}
                @else
                {{'Not provided'}}
                @endif
            </div>
          </div>
        </div>
      </div>
</div>

@stop
