@extends('trainerLayouts.master')

@section('title')
Queue
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">


@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Queue
@endsection
<form class="form" role="form" method="POST" action="{{ url('trainer/queues/delete')  }}" >  
    {{ csrf_field() }} 
    <div class="row">
        <div class="col-md-12">
            @include('trainerLayouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">
          @extends('trainerLayouts.master')

@section('title')
Queue
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">


@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Queue
@endsection
<form class="form" role="form" method="POST" action="{{ url('trainer/queues/delete')  }}" >  
    {{ csrf_field() }} 
    <div class="row">
        <div class="col-md-12">
            @include('trainerLayouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">

         
                <div class="panel-heading">
                   
                    <div class="panel-options">
                      
                        <a href="{{ url('trainer/queues/create')  }}" class="margin-top0">
                            <button type="button" class="btn btn-default btn-icon">
                                Add Record
                                <i class="entypo-plus padding10"></i>
                            </button>
                        </a>
                        
                        <button Onclick="return ConfirmDelete();" type="button" class="btn btn-red btn-icon">
                            Delete
                            <i class="entypo-cancel"></i>
                        </button>
                       
                    </div>
                    <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" --> <li class="active"> <a href="#home" data-toggle="tab"> <span class="visible-xs"><i class="entypo-home"></i></span> <span class="hidden-xs">Home</span> </a> </li> <li> <a href="#profile" data-toggle="tab"> <span class="visible-xs"><i class="entypo-user"></i></span> <span class="hidden-xs">Profile</span> </a> </li> <li> <a href="#messages" data-toggle="tab"> <span class="visible-xs"><i class="entypo-mail"></i></span> <span class="hidden-xs">Messages</span> </a> </li> <li> <a href="#settings" data-toggle="tab"> <span class="visible-xs"><i class="entypo-cog"></i></span> <span class="hidden-xs">Settings</span> </a> </li> </ul>
                </div>

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="text-center" id="td_checkbox"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>
                                <th class="col-sm-3">Branch Name</th>
                                <th class="col-sm-2">Service</th>
                                <th class="col-sm-1">start time</th>
                                {{-- <th class="text-center">Actions</th> --}}
                            </tr>
                        </thead>


                    </table>
                </div>

            </div>

        </div>
    </div>
</form>
@endsection

@section('scripts')

<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                var $table4 = jQuery("#table-4");
                                $table4.DataTable({
                                    dom: 'lBfrtip',
                                    "stateSave": true,
                                    processing: true,
                                    serverSide: true,
                                    ordering: true,
                                    language: {
                                        processing: "<img src='{{ asset('assets/images/loader-1.gif') }}'>"
                                    },
                                    "ajax": {
                                        "type": "GET",
                                        @if ($service_id != 0)
                                            "url":  '{{ url("trainer/queues/$service_id/view") }}',
                                            @else
                                            "url": '{{ url("trainer/queues") }}',
                                            @endif
                                       
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'id', class: 'text-center checkbox_padding',orderable: false, searchable: false},
                                        {data: 1, name: 'branch_id'},
                                        {data: 2, name: 'service_id', class: 'text-center'},
                                        {data: 3, name: 'starttime', class: 'text-center'},                                     
                                    ],
                                    order: [[1, 'desc']],
                                    "fnDrawCallback": function (oSettings) {
                                        $('input.icheck-14').iCheck({
                                            checkboxClass: 'icheckbox_polaris',
                                            radioClass: 'iradio_polaris'
                                        });
                                        $('#check-all').on('ifChecked', function (event) {
                                            $('.check').iCheck('check');
                                            return false;
                                        });
                                        $('#check-all').on('ifUnchecked', function (event) {
                                            $('.check').iCheck('uncheck');
                                            return false;
                                        });
// Removed the checked state from "All" if any checkbox is unchecked
                                        $('#check-all').on('ifChanged', function (event) {
                                            if (!this.changed) {
                                                this.changed = true;
                                                $('#check-all').iCheck('check');
                                            } else {
                                                this.changed = false;
                                                $('#check-all').iCheck('uncheck');
                                            }
                                            $('#check-all').iCheck('update');
                                        });
                                        /*----Status Update---*/
                                        $('.status').on('click', function (e) {
                                            e.preventDefault();
                                            var ID = $(this).attr('sid');
                                            var Value = $(this).attr('value');
                                            $.ajax({
                                                type: "PATCH",
                                                async: true,
                                                url: "{{ url('trainer/queues')}}/"+ID,
                                                data: {id: ID, status: Value, _token: '{{ csrf_token() }}'},
                                                success: function (data) {
                                                    $table4.DataTable().ajax.reload(null, false);
                                                    toastr.success(data.response, "", opts);
                                                }
                                            });
                                        });
                                        /*------END----*/
                                    },
                                    buttons: [
                                        //'copyHtml5',
                                        'excelHtml5',
                                        'csvHtml5',
                                        'pdfHtml5'
                                    ]
                                });
                                // Sample Toastr Notification
                                var opts = {
                                    "closeButton": true,
                                    "debug": false,
                                    "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
                                    "toastClass": "sucess",
                                    "onclick": null,
                                    "showDuration": "300",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "fadeIn",
                                    "hideMethod": "fadeOut"
                                };
                            });</script>
<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        $('input.icheck-14').iCheck({
            checkboxClass: 'icheckbox_polaris',
            radioClass: 'iradio_polaris'
        });
        /*---CheckAll---*/
        $('#check-all').on('ifChecked', function (event) {
            $('.check').iCheck('check');
            return false;
        });
        $('#check-all').on('ifUnchecked', function (event) {
            $('.check').iCheck('uncheck');
            return false;
        });
// Removed the checked state from "All" if any checkbox is unchecked
        $('#check-all').on('ifChanged', function (event) {
            if (!this.changed) {
                this.changed = true;
                $('#check-all').iCheck('check');
            } else {
                this.changed = false;
                $('#check-all').iCheck('uncheck');
            }
            $('#check-all').iCheck('update');
        });
        /*------END----*/


    });
    /*---On Delete All Confirmation---*/
    function ConfirmDelete() {
        var chkId = '';
        $('.check:checked').each(function () {
            chkId = $(this).val();
        });
        if (chkId == '') {
            alert('{{ config('global.deleteCheck') }}');
            return false;
        } else {
            if (confirm('{{ config('global.deleteConfirmation') }}')) {
                 $('.form').submit();
            } else {
                return false;
            }
        }

    }
    /*------END----*/
</script>



@endsection

                <div class="panel-heading">

                    <div class="panel-options">
                      
                        <a href="{{ url('trainer/queues/create')  }}" class="margin-top0">
                            <button type="button" class="btn btn-default btn-icon">
                                Add Record
                                <i class="entypo-plus padding10"></i>
                            </button>
                        </a>
                        
                        <button Onclick="return ConfirmDelete();" type="button" class="btn btn-red btn-icon">
                            Delete
                            <i class="entypo-cancel"></i>
                        </button>
                       
                    </div>
                </div>

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="text-center" id="td_checkbox"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>
                                <th class="col-sm-3">Branch Name</th>
                                <th class="col-sm-2">Service</th>
                                <th class="col-sm-1">start time</th>
                                {{-- <th class="text-center">Actions</th> --}}
                            </tr>
                        </thead>


                    </table>
                </div>

            </div>

        </div>
    </div>
</form>
@endsection

@section('scripts')

<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                var $table4 = jQuery("#table-4");
                                $table4.DataTable({
                                    dom: 'lBfrtip',
                                    "stateSave": true,
                                    processing: true,
                                    serverSide: true,
                                    ordering: true,
                                    language: {
                                        processing: "<img src='{{ asset('assets/images/loader-1.gif') }}'>"
                                    },
                                    "ajax": {
                                        "type": "GET",
                                        @if ($service_id != 0)
                                            "url":  '{{ url("trainer/queues/$service_id/view") }}',
                                            @else
                                            "url": '{{ url("trainer/queues") }}',
                                            @endif
                                       
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'id', class: 'text-center checkbox_padding',orderable: false, searchable: false},
                                        {data: 1, name: 'branch_id'},
                                        {data: 2, name: 'service_id', class: 'text-center'},
                                        {data: 3, name: 'starttime', class: 'text-center'},                                     
                                    ],
                                    order: [[1, 'desc']],
                                    "fnDrawCallback": function (oSettings) {
                                        $('input.icheck-14').iCheck({
                                            checkboxClass: 'icheckbox_polaris',
                                            radioClass: 'iradio_polaris'
                                        });
                                        $('#check-all').on('ifChecked', function (event) {
                                            $('.check').iCheck('check');
                                            return false;
                                        });
                                        $('#check-all').on('ifUnchecked', function (event) {
                                            $('.check').iCheck('uncheck');
                                            return false;
                                        });
// Removed the checked state from "All" if any checkbox is unchecked
                                        $('#check-all').on('ifChanged', function (event) {
                                            if (!this.changed) {
                                                this.changed = true;
                                                $('#check-all').iCheck('check');
                                            } else {
                                                this.changed = false;
                                                $('#check-all').iCheck('uncheck');
                                            }
                                            $('#check-all').iCheck('update');
                                        });
                                        /*----Status Update---*/
                                        $('.status').on('click', function (e) {
                                            e.preventDefault();
                                            var ID = $(this).attr('sid');
                                            var Value = $(this).attr('value');
                                            $.ajax({
                                                type: "PATCH",
                                                async: true,
                                                url: "{{ url('trainer/queues')}}/"+ID,
                                                data: {id: ID, status: Value, _token: '{{ csrf_token() }}'},
                                                success: function (data) {
                                                    $table4.DataTable().ajax.reload(null, false);
                                                    toastr.success(data.response, "", opts);
                                                }
                                            });
                                        });
                                        /*------END----*/
                                    },
                                    buttons: [
                                        //'copyHtml5',
                                        'excelHtml5',
                                        'csvHtml5',
                                        'pdfHtml5'
                                    ]
                                });
                                // Sample Toastr Notification
                                var opts = {
                                    "closeButton": true,
                                    "debug": false,
                                    "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
                                    "toastClass": "sucess",
                                    "onclick": null,
                                    "showDuration": "300",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "fadeIn",
                                    "hideMethod": "fadeOut"
                                };
                            });</script>
<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        $('input.icheck-14').iCheck({
            checkboxClass: 'icheckbox_polaris',
            radioClass: 'iradio_polaris'
        });
        /*---CheckAll---*/
        $('#check-all').on('ifChecked', function (event) {
            $('.check').iCheck('check');
            return false;
        });
        $('#check-all').on('ifUnchecked', function (event) {
            $('.check').iCheck('uncheck');
            return false;
        });
// Removed the checked state from "All" if any checkbox is unchecked
        $('#check-all').on('ifChanged', function (event) {
            if (!this.changed) {
                this.changed = true;
                $('#check-all').iCheck('check');
            } else {
                this.changed = false;
                $('#check-all').iCheck('uncheck');
            }
            $('#check-all').iCheck('update');
        });
        /*------END----*/


    });
    /*---On Delete All Confirmation---*/
    function ConfirmDelete() {
        var chkId = '';
        $('.check:checked').each(function () {
            chkId = $(this).val();
        });
        if (chkId == '') {
            alert('{{ config('global.deleteCheck') }}');
            return false;
        } else {
            if (confirm('{{ config('global.deleteConfirmation') }}')) {
                 $('.form').submit();
            } else {
                return false;
            }
        }

    }
    /*------END----*/
</script>



@endsection