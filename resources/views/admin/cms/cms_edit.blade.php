@extends('admin.layouts.master')

@section('content')
<div class="">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Edit Cms <a href="{{route('cms.index')}}" class="btn btn-info btn-xs"><i class="fa fa-chevron-left"></i> Back </a></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form method="post" action="{{ route('cms.update', ['id' => $cms->id]) }}" data-parsley-validate class="form-horizontal form-label-left">
                        <div class="form-group{{ $errors->has('cms') ? ' has-error' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cms">Title <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" value="{{$cms->title}}" id="title" name="title" class="form-control col-md-7 col-xs-12">
                                @if ($errors->has('title'))
                                <span class="help-block">{{ $errors->first('title') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('discription') ? ' has-error' : '' }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description">Description <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                               {{--  <input type="text" value="{{$cms->description}}" id="description" name="description" class="form-control col-md-7 col-xs-12"> --}}
                                <textarea  id="description" name="description" class="form-control col-md-7 col-xs-12" rows="8" cols="8">
                                {{$cms->description}}
                                </textarea>
                                @if ($errors->has('description'))
                                <span class="help-block">{{ $errors->first('description') }}</span>
                                @endif
                            </div>
                        </div> 

                        <div class="ln_solid"></div>

                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                                
                                <input name="_method" type="hidden" value="PUT">
                                <button type="submit" class="btn btn-success">Save Cms Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop