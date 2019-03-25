
<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::check()?Auth::user()->id:'' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Robust admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, robust admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    {{-- <title>Dashboard</title> --}}
    <title>
    @if(!empty($title))
    {{$title}}
    @else
   {{config('app.name')}}
    @endif
    </title>
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('backend/app-assets/images/ico/apple-icon-60.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('backend/app-assets/images/ico/apple-icon-76.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('backend/app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('backend/app-assets/images/ico/apple-icon-152.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('backend/app-assets/images/ico/favicon.ico') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('backend/app-assets/images/ico/favicon-32.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/css/bootstrap.css') }}">
    <link href="{{asset('/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <!-- font icons-->
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/fonts/icomoon.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/fonts/flag-icon-css/css/flag-icon.min.css') }}"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/vendors/css/extensions/pace.css') }}"> --}}
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/css/colors.css') }}">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/css/core/menu/menu-types/vertical-overlay-menu.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('backend/app-assets/css/pages/login-register.css') }}"> --}}
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/style.css') }}">

 <link href="{{asset('/css/custom.css')}}" rel="stylesheet">
    <!-- Datatables -->
     <link href="{{asset('/css/dataTables.bootstrap.min.css')}}" rel="stylesheet"> 
    <link href="{{asset('/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
     <link href="{{asset('/css/scroller.bootstrap.min.css')}}" rel="stylesheet">


    <link href="{{asset('/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{asset('/css/font-awesome.min.css')}}" rel="stylesheet">
    
    <!-- END Custom CSS-->
  </head>
 <body data-open="click" data-menu="vertical-menu" data-col="2-column" class="vertical-layout vertical-menu 2-columns  fixed-navbar  menu-expanded pace-done">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
       @include('admin.partials.header')
       @include('admin.partials.sidebar')
    <div class="app-content content container-fluid">
      <div class="content-wrapper">
          @include('admin.partials.alerts')
	      @yield('content')

      </div>
    </div>
    

    <script src="{{ asset('backend/app-assets/js/core/libraries/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/vendors/js/ui/tether.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/js/core/libraries/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/vendors/js/ui/unison.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/vendors/js/ui/blockUI.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/vendors/js/ui/jquery.matchHeight-min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/vendors/js/ui/screenfull.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/vendors/js/extensions/pace.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/js/core/app-menu.js') }}" type="text/javascript"></script>
    <script src="{{ asset('backend/app-assets/js/core/app.js') }}" type="text/javascript"></script>
      <!-- Datatables -->
    <script src="{{asset('/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('/js/buttons.bootstrap.min.js')}}"></script>
    {{-- <script src="{{asset('/js/buttons.flash.min.js')}}"></script> --}}
    <script src="{{asset('/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('/js/buttons.print.min.js')}}"></script>
 {{--    <script src="{{asset('/js/dataTables.fixedHeader.min.js')}}"></script> --}}
    {{-- <script src="{{asset('/js/dataTables.keyTable.min.js')}}"></script> --}}
   {{--  <script src="{{asset('/js/dataTables.responsive.min.js')}}"></script> --}}
    {{-- <script src="{{asset('/js/responsive.bootstrap.js')}}"></script> --}}
    {{-- <script src="{{asset('/js/dataTables.scroller.min.js')}}"></script> --}}
   {{--  <script src="{{asset('/js/jszip.min.js')}}"></script> --}}
    {{-- <script src="{{asset('/js/pdfmake.min.js')}}"></script> --}}
   {{--  <script src="{{asset('/js/vfs_fonts.js')}}"></script> --}}


        <!-- Datatables -->
    <script>
        $(document).ready(function() {
            var handleDataTableButtons = function() {
                if ($("#datatable-buttons").length) {
                    $("#datatable-buttons").DataTable({
                        dom: "Bfrtip",
                        buttons: [
                        {
                            extend: "copy",
                            className: "btn-sm"
                        },
                        {
                            extend: "csv",
                            className: "btn-sm"
                        },
                        {
                            extend: "excel",
                            className: "btn-sm"
                        },
                        {
                            extend: "pdfHtml5",
                            className: "btn-sm"
                        },
                        {
                            extend: "print",
                            className: "btn-sm"
                        },
                        ],
                columnDefs:[
                        { 
                            orderSequence:["desc"], 
                            targets:[0],
                            // orderable:!1,
                        } ,
                        ],
                        responsive: true
                    });
                }
            };

            TableManageButtons = function() {
                "use strict";
                return {
                    init: function() {
                        handleDataTableButtons();
                    }
                };
            }();

            $('#datatable').dataTable();

            $('#datatable-keytable').DataTable({
                keys: true
            });

            $('#datatable-responsive').DataTable();

            $('#datatable-scroller').DataTable({
                ajax: "js/datatables/json/scroller-demo.json",
                deferRender: true,
                scrollY: 380,
                scrollCollapse: true,
                scroller: true
            });

            $('#datatable-fixed-header').DataTable({
                fixedHeader: true
            });

            var $datatable = $('#datatable-checkbox');

            $datatable.dataTable({
                'order': [[ 1, 'asc' ]],
                'columnDefs': [
                { orderable: false, targets: [0] }
                ]
            });
            $datatable.on('draw.dt', function() {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_flat-green'
                });
            });

            TableManageButtons.init();
        });
    </script>
    <!-- /Datatables -->
  {{--   <script>
        $(document).ready( function () {
          var table = $('#datatable-buttons').DataTable({
            columnDefs:[
                { orderSequence:["desc"], targets:[0]} ,
            ]
          } );
        } );
    </script> --}}
  </body>
</html>

