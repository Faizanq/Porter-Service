@extends('admin.layouts.login')
@section('content')
<section class="flexbox-container">
    <div class="col-md-4 offset-md-4 col-xs-10 offset-xs-1 box-shadow-2 p-0">
        <div class="card border-grey border-lighten-3 px-2 py-2 m-0">
            <div class="card-header no-border pb-0">
                <div class="card-title text-xs-center">
                    {{-- <img src="../../app-assets/images/logo/robust-logo-dark.png" alt="branding logo"> --}}
                    <h2>{{config('app.name')}}</h2>
                </div>
                <h6 class="card-subtitle line-on-side text-muted text-xs-center font-small-3 pt-2"><span>We will send you a link to reset your password.</span></h6>
            </div>
            <div class="card-body collapse in">
            @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                <div class="card-block">
                    <form class="form-horizontal" method="POST" action="{{ route('admin.password.email') }}" novalidate>
                        <fieldset class="form-group position-relative has-icon-left {{ $errors->has('email') ? ' has-error' : '' }}">
                         {{ csrf_field() }}
                            <input type="email" class="form-control form-control-lg input-lg" id="user-email" placeholder="Your Email Address" name="email"  required value="{{ old('email') }}" required>
                             @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            <div class="form-control-position">
                                <i class="icon-mail6"></i>
                            </div>
                        </fieldset>
                        <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="icon-lock4"></i> Recover Password</button>
                    </form>
                </div>
            </div>
            <div class="card-footer no-border">
                <p class="float-sm-left text-xs-center"><a href="/admin/login" class="card-link">Login</a></p>
            </div>
        </div>
    </div>
</section>
@stop