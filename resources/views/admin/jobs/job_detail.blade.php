<link rel="stylesheet" href="/css/profile.css">

@extends('admin.layouts.master')

@section('content')

<div class="row">
      <div class="col-xs-12 col-sm-9">
        
        <!-- Empoloyer profile -->
        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">Employer Detail</h4>
          </div>
          @if($job->user->profile_image != null && $job->user->profile_image !='')
            @php
            $image = $job->user->profile_image;
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
              <h4>{{$job->user->full_name}}</h4>
              <p class="text-muted">
                @if(!empty($job->user->about))
                  {{$job->user->about}}
                @else
                  {{'No description'}}
                @endif
              </p>
              <p>
                <a href="#">{{$job->user->email}}</a>
              </p>
            </div>
          </div>
        </div>

        <!-- Job info -->
        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">Job info</h4>
          </div>
          @if($job->work_place_image != null && $job->work_place_image !='')
            @php
            $image = $job->work_place_image;
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
              @if(!empty($job->position))
              {{$job->position}}
              @else
              {{'---'}}
              @endif 
              <strong>
              	@if($job->status)
              		<span class="label bg-green">OPEN</span>
              	@else
              		<span class="label bg-red">CLOSED</span>
              	@endif
              </strong>
              
              <small></small></h4>
              <p class="text-muted">
                @if(!empty($job->description))
                  {{$job->description}}
                @else
                  {{'No description'}}
                @endif
              </p>
            </div>
          <div class="panel-body">
            <table class="table profile__table">
              <tbody>
              <tr>
                  <th><strong>Job Type</strong></th>
                  <td>
                  @if($job->job_type == 1)
                  {{'Part Time'}}
                  @else
                  {{'Full Time'}}
                  @endif
                  </td>
               </tr>

              <tr>
                  <th><strong>Company Name</strong></th>
                  <td>
                  @if(!empty($job->company))
                  {{$job->company->name}}
                  @else
                  {{'---'}}
                  @endif
                  </td>
               </tr>
               <tr>
                  <th><strong>Category</strong></th>
                  <td>
                  @if(!empty($job->category))
                  {{$job->category->name}}
                  @else
                  {{'---'}}
                  @endif
                  </td>
               </tr>
               <tr>
                    <th><strong>Location</strong></th>
                    <td>
                    @if(!empty($job->location))
                    {{$job->location}}
                    @else
                    {{'Not provided'}}
                    @endif
                    </td>
                </tr>
                <tr>
                  <th><strong>Immediate Start</strong></th>
                  <td>{{$job->immediate_start?'Yes':'No'}}</td>
                </tr>

                <tr>
                  <th><strong>Salary Type</strong></th>
                  <td>{{$salaryType[$job->salary_type]}}</td>
                </tr>

                <tr>
                  <th><strong>Minimum Salary</strong></th>
                  <td>{{$job->salary_min}}</td>
                </tr>

                <tr>
                  <th><strong>Maximum Salary</strong></th>
                  <td>{{$job->salary_max}}</td>
                </tr>

                <tr>
                  <th><strong>Shift Schedule</strong></th>
                  <td>{{$job->shift_schedule}}</td>
                </tr>

                <tr>
                  <th><strong>Extra Compensation</strong></th>
                  <td>{{$job->extra_compensation?'Yes':'No'}}</td>
                </tr>

                <tr>
                  <th><strong>Is Experience Require</strong></th>
                  <td>{{$job->is_experience_require?'Yes':'No'}}</td>
                </tr>

              </tbody>
            </table>
          </div>
          </div>
        </div>

        <!-- Community -->
        <div class="panel panel-default">
          <div class="panel-heading">
          <h4 class="panel-title">Community</h4>
          </div>
          <div class="panel-body">
            <table class="table profile__table">
              <tbody>
                <tr>
                  <th><strong>No of Applicant Applied</strong></th>
                  @if(!empty($job->apply))
                  <td>{{count($job->apply)}}</td>
                  @else
                  {{'0'}}
                  @endif
                </tr>
                <tr>
                  <th><strong>Posted since</strong></th>
                  <td>{{ \Carbon\Carbon::parse($job->created_at)->format('M d Y')}}</td>
                </tr>
              </tbody>
            </table>
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
              <h5 class="profile__contact-info-heading">Work number</h5>
              @if(!empty($job->user->contact_number))
              ({{$job->user->country_code}}){{$job->user->contact_number}}
              @else
              {{'Not provided'}}
              @endif
            </div>
          </div>
          <div class="profile__contact-info-item">
            <div class="profile__contact-info-icon">
              <i class="fa fa-phone"></i>
            </div>
            <div class="profile__contact-info-body">
              <h5 class="profile__contact-info-heading">Mobile number</h5>
              @if(!empty($job->user->contact_number))
              ({{$job->user->country_code}}){{$job->user->contact_number}}
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
              <a href="mailto:{{$job->user->email}}">{{$job->user->email}}</a>
            </div>
          </div>
          <div class="profile__contact-info-item">
            <div class="profile__contact-info-icon">
              <i class="fa fa-map-marker"></i>
            </div>
            <div class="profile__contact-info-body">
              <h5 class="profile__contact-info-heading">Work address</h5>
              @if(!empty($job->user->location))
              {{$job->user->location}}
              @else
              {{'Not provided'}}
              @endif
            </div>
          </div>
        </div>

      </div>
    </div>
@stop