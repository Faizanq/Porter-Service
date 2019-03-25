@extends('admin.layouts.master')

@section('content')
<div class="">

    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                {{-- <div class="x_title">
                    <h2>Cms <a href="{{route('cms.create')}}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Create New </a></h2>
                    <div class="clearfix"></div>
                </div> --}}
                <div class="x_content">
                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($cms))
                            @foreach($cms as $row)
                            <tr>
                                <td>{{$row->title}}</td>
                                <td>{{$row->description}}</td>
                                @if($row->status)
                                <td><a href="javascript:void(0);" class="label bg-green" onclick="status({{$row->id}},0)" id="status{{$row->id}}0">Active</a></td>
                                @else 
                                <td><a href="javascript:void(0);" class="label bg-red" onclick="status({{$row->id}},1)" id="status{{$row->id}}1">In-Active</a></td>
                                @endif
                                <td>
                                    <a href="{{ route('cms.edit', ['id' => $row->id]) }}" class="btn btn-info btn-xs"><i class="icon-pencil" title="Edit"></i> </a>
                                    {{-- <a href="{{ route('cms.show', ['id' => $row->id]) }}" class="btn btn-danger btn-xs"><i class="icon-bin" title="Delete"></i> </a> --}}
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


     alert(id,state);

     $.ajax({

        method:'GET',
        url:'/admin/cms/active',
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
            location.reload();
        },
        error:function(){
            console.log('error');
        },
    });
 }
</script>