@extends('admin.layouts.master')

@section('content')

<script src="https://maps.googleapis.com/maps/api/js
?key=AIzaSyDQ8iXfakJYWPT3XE8TNPfjqn1J8MBlthw&?sensor=true&libraries=places
"
  type="text/javascript"></script>

</head>
<div class="">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Create Storage <a href="{{route('storages.index')}}" class="btn btn-info btn-xs"><i class="fa fa-chevron-left"></i> Back </a></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form method="post" action="{{ route('storages.store') }}" data-parsley-validate class="form-horizontal form-label-left">

                        <div class="top-margin form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="brand">Address <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" value="{{ Request::old('address') ?: '' }}" id="address" name="address" class="form-control col-md-7 col-xs-12" placeholder="Enter a location">
                                @if ($errors->has('address'))
                                <span class="help-block">{{ $errors->first('address') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('latitude') ? ' has-error' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="latitude">latitude <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" value="{{ Request::old('latitude') ?: '' }}" id="latitude" name="latitude" class="form-control col-md-7 col-xs-12">
                                @if ($errors->has('latitude'))
                                <span class="help-block">{{ $errors->first('latitude') }}</span>
                                @endif
                            </div>
                        </div>

                         <div class="form-group{{ $errors->has('longitude') ? ' has-error' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="longitude">longitude <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" value="{{ Request::old('longitude') ?: '' }}" id="longitude" name="longitude" class="form-control col-md-7 col-xs-12">
                                @if ($errors->has('longitude'))
                                <span class="help-block">{{ $errors->first('longitude') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="ln_solid"></div>

                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                                <button type="submit" class="btn btn-success">Create Storage</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>

    function initialize() {
        var input = document.getElementById('address');
        var autocomplete = new google.maps.places.Autocomplete(input);

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            console.log(place);

            document.getElementById('address').value = place.name;
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();

            alert("This function is working!");
            alert(place.name);
            alert(place.address_components[0].long_name);

        });
    }
    google.maps.event.addDomListener(window, 'load', initialize); 

</script>

@stop

