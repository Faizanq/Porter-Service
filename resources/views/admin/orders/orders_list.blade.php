@extends('admin.layouts.master')

@section('content')
<div class="">

    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Requests {{-- <a href="{{route('orders.create')}}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Create New </a> --}}</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Order Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($orders))
                            @foreach($orders as $row)
                            <tr>
                                <td>{{$row->id}}</td>
                                <td>{{$row->user->full_name?$row->user->full_name:''}}</td>
                                <td>{{$row->user->email?$row->user->email:''}}</td>
                                <td>{{$row->user->country_code?$row->user->country_code:''}}{{$row->user->mobile_number?$row->user->mobile_number:''}}</td>
                                <td>{{$row->created_at}}</td>
                                <td>{{$status[$row->status]}}</td>
                                <td>{{$row->is_pony_service == 'Y' ? 'Pony Service':'Porter Service'}}</td>                             
                                <td>
                                     <a href="{{ route('orders.profile', ['id' => $row->id]) }}" class="btn btn-info btn-xs"><i class="icon-eye" title="View"></i> </a>

                                   {{--  <a href="{{ route('orders.edit', ['id' => $row->id]) }}" class="btn btn-info btn-xs"><i class="icon-pencil" title="Edit"></i> </a> --}}
                                    <a href="{{ route('orders.show', ['id' => $row->id]) }}" class="btn btn-danger btn-xs"><i class="icon-bin" title="Delete"></i> </a>
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
            // if(parseInt(data.status)){
            //         $("#status"+id+state).removeClass("bg-red").addClass("bg-green");
            //         $("#status"+id+state).val('Active')
            //     }
            // else {
            //     $("#status"+id+state).removeClass("bg-green").addClass("bg-red");
            //     $("#status"+id+state).val('In-Active')
            // }
            location.reload();
        },
        error:function(){
            console.log('error');
            console.log(data);
        },
});
 }


</script>