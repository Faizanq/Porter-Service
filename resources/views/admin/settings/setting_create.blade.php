@extends('admin.layouts.master')

@section('content')

</head>
<div class="">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Update Settings <a href="{{route('admin')}}" class="btn btn-info btn-xs"><i class="fa fa-chevron-left"></i> Back </a></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form method="post" action="{{ route('settings.store') }}" data-parsley-validate class="form-horizontal form-label-left">
    
                    @foreach($settings as $setting)
                    

                        <div class="top-margin form-group{{ $errors->has($setting->key) ? ' has-error' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="{{$setting->key}}">{{$setting->name}}<span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" value="{{$setting->value}}" id="{{$setting->key}}" name="{{$setting->key}}" class="form-control col-md-7 col-xs-12">
                                @if ($errors->has('{{$setting->key}}'))
                                <span class="help-block">{{ $errors->first('key') }}</span>
                                @endif
                            </div>
                        </div>

                    @endforeach

                        <div class="ln_solid"></div>

                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                                <button type="submit" class="btn btn-success">Save Settings</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@stop

