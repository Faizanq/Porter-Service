@extends('admin.layouts.master')

@section('content')
<div class="">
    <div class="clearfix"></div>

    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Confirm Delete Record <a href="{{route('cms.index')}}" class="btn btn-info btn-xs"><i class="fa fa-chevron-left"></i> Back </a></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <p>Are you sure you want to delete <strong>{{$cms->title}}</strong></p>

                    <form method="POST" action="{{ route('cms.destroy', ['id' => $cms->id]) }}">
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                        <input name="_method" type="hidden" value="DELETE">
                        <button type="submit" class="btn btn-danger">Yes I'm sure. Delete <strong>{{$cms->title}}</strong></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop