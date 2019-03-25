@extends('admin.layouts.login')
@section('content')

<section class="flexbox-container">
    <div class="col-md-4 offset-md-4 col-xs-10 offset-xs-1  box-shadow-2 p-0">
        <div class="card border-grey border-lighten-3 m-0">
            <div class="card-header no-border">
                <div class="card-title text-xs-center">
                    <div class="p-1">
                  {{--   <img src="{{ asset('backend/app-assets/images/logo/robust-logo-dark.png') }}" alt="branding logo"> --}}
	                  <h2>{{config('app.name')}}</h2>
                    </div>
                </div>
                <h6 class="card-subtitle line-on-side text-muted text-xs-center font-small-3 pt-2"><span>Login with {{config('app.name')}}</span></h6>
            </div>
            <div class="card-body collapse in">
                <div class="card-block">
                    <form class="form-horizontal form-simple" action="{{ route('admin-login') }}" novalidate method="post">
                    {{ csrf_field() }}
                        <fieldset class="form-group position-relative has-icon-left mb-0 {{ $errors->has('email') ? ' has-error' : '' }}">
                            <input type="email" class="form-control form-control-lg input-lg" id="user-name" placeholder="Your Username" required name="email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                             
                            <div class="form-control-position">
                                <i class="icon-head"></i>
                            </div>
                        </fieldset>
                        <fieldset class="form-group position-relative has-icon-left {{ $errors->has('password') ? ' has-error' : '' }}">
                            <input type="password" class="form-control form-control-lg input-lg" id="user-password" placeholder="Enter Password" required name="password">

                            @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                
                            <div class="form-control-position">
                                <i class="icon-key3"></i>
                            </div>
                        </fieldset>
                        <fieldset class="form-group row">
                            <div class="col-md-6 col-xs-12 text-xs-center text-md-left">
                                <fieldset>
                                    <input type="checkbox" id="remember-me" class="chk-remember">
                                    <label for="remember-me"> Remember Me</label>
                                </fieldset>
                            </div>
                            <div class="col-md-6 col-xs-12 text-xs-center text-md-right"><a href="{{route('admin.password.request')}}" class="card-link">Forgot Password?</a></div>
                        </fieldset>
                        <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="icon-unlock2"></i> Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop