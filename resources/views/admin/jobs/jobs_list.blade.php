@extends('admin.layouts.master')

@section('content')
<div class="">

    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                {{-- <div class="x_title">
                    <h2>Jobs <a href="{{route('states.create')}}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Create New </a></h2>
                    <div class="clearfix"></div>
                </div> --}}
                <div class="x_content">
                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Post By</th>
                                <th>Post Date</th>
                                <th>No of Applicant Applied</th>
                                <th>Position</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($jobs))
                            @foreach($jobs as $row)
                            <tr>
                                <td>{{$row->user->full_name}}</td>
                                <td>{{$row->created_at}}</td>
                                <td><a href="{{route('applicants.index',['id'=>$row->id])}}">{{count($row->apply)}}</a></td>

                                <td>{{$row->position}}</td>
                                <td>{{$row->location}}</td>
                                @if($row->status)
                                <td><a href="javascript:void(0);" class="label bg-green" onclick="status({{$row->id}},0)" id="status{{$row->id}}0">Active</a></td>
                                @else 
                                <td><a href="javascript:void(0);" class="label bg-red" onclick="status({{$row->id}},1)" id="status{{$row->id}}1">In-Active</a></td>
                                @endif
                                <td>
                                     <a href="{{ route('jobs.detail', ['id' => $row->id]) }}" class="btn btn-info btn-xs"><i class="icon-eye" title="View"></i> </a> 
                                    {{-- <a href="{{ route('states.show', ['id' => $row->id]) }}" class="btn btn-danger btn-xs"><i class="icon-bin" title="Delete"></i> </a> --}}
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

<script>

function status(id,state) {

     $.ajax({

        method:'POST',
        url:window.location.href+'/active',
        dataType: 'JSON',
        data: {
            "id": id,
            "status": state
        },
        beforeSend: function (xhr) {
            var token = "{{ csrf_token() }}";

            if (token) {
                  return xhr.setRequestHeader('X-CSRF-TOKEN', token);
            }
        },
        success:function(data){
            console.log('success');
            console.log(data);
            location.reload();
        },
        error:function(){
            console.log('error');
            console.log(data);
        },
    });
 }
</script>