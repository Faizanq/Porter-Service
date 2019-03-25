@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{$terms->title}}</div>

                <div class="panel-body">
                
                <h2>{{$terms->description}}</h2>
                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
