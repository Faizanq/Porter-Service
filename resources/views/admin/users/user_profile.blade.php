<link rel="stylesheet" href="/css/profile.css">

@extends('admin.layouts.master')

@section('content')

<div class="row">
      <div class="col-xs-12 col-sm-9">
        
        <!-- user profile -->
        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">user profile</h4>
          </div>
          @if($user->profile_image != null && $user->profile_image !='')
            @php
            $image = $user->profile_image;
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
              <h4>{{$user->full_name}}<small></small></h4>
              <p class="text-muted">
                @if(!empty($user->about))
                  {{$user->about}}
                @else
                  {{'No description'}}
                @endif
              </p>
              <p>
                <a href="#">{{$user->email}}</a>
              </p>
            </div>
          </div>
        </div>
        <!-- Latest order posts -->
        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">Latest Orders</h4>
          </div>
          <div class="panel-body">
            <div class="profile__comments">
              @foreach($orders as $order)
              <div class="profile-comments__item">
                <div class="profile-comments__controls">
                  <a href="{{route('order.detail',['id'=>$order->id])}}"><i class="fa fa-share-square-o"></i></a>
                </div>

                <div class="profile-comments__body"> 
                  <h5 class="profile-comments__sender">
                    @if(!empty($order->bagages))
                    {{$order->bagages}}
                    @else
                    {{'---'}}
                    @endif
                    <small>{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans()}}</small>
                  </h5>
                  <div class="profile-comments__content">
                    {{$order->description}}
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
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
              @if(!empty($user->contact_number))
              ({{$user->country_code}}){{$user->contact_number}}
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
              <a href="mailto:{{$user->email}}">{{$user->email}}</a>
            </div>
          </div>
          <div class="profile__contact-info-item">
            <div class="profile__contact-info-icon">
              <i class="fa fa-map-marker"></i>
            </div>
            <div class="profile__contact-info-body">
              <h5 class="profile__contact-info-heading">Address</h5>
              @if(!empty($user->location))
              {{$user->location}}
              @else
              {{'Not provided'}}
              @endif
            </div>
          </div>
        </div>

      </div>
    </div>
@stop