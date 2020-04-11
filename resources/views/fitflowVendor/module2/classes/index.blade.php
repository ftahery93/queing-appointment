@extends('vendorLayouts.master')

@section('title')
Branch wise Class - {{  $className }}
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
@if($EditAccess!=1)
<style>
    table tr th:last-child, table tr td:last-child{display:none;}
</style>
@endif
@if($DeleteAccess!=1)
<style>
    table tr th:first-child, table tr td:first-child{display:none;}
</style>
@endif

@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM2.'/classMaster') }}"> {{  $className }}</a>
</li>
@endsection

@section('pageheading')
Branch wise Class - {{  $className }}
@endsection
<form class="form" role="form" method="POST" action="{{ url($configM2.'/'.$classMasterID.'/classes/delete')  }}" >  
    {{ csrf_field() }} 
    <div class="row">
        <div class="col-md-12">
            @include('vendorLayouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">


                <div class="panel-heading">

                    <div class="panel-options">
                        @if ($CreateAccess==1)
                        <a href="{{ url($configM2.'/'.$classMasterID.'/classes/create')  }}" class="margin-top0">
                            <button type="button" class="btn btn-default btn-icon">
                                Add Record
                                <i class="entypo-plus padding10"></i>
                            </button>
                        </a>
                        @endif

                        @if ($DeleteAccess==1)
                        <button Onclick="return ConfirmDelete();" type="button" class="btn btn-red btn-icon">
                            Delete
                            <i class="entypo-cancel"></i>
                        </button>
                        @endif

                    </div>
                </div>

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="text-center" id="td_checkbox"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>
                                <th class="col-sm-3">Branch</th>
                                <th class="text-center col-sm-1">Total Seats</th> 
                                <th class="text-center col-sm-1">Gym Seats</th>                                                               
                                <th class="text-center col-sm-1">{{ $appTitle->title }} Seats</th>
                                <th class="text-center col-sm-1">Status</th>
                                <th class="text-center col-sm-1">Created On</th>
                                <th class="text-center col-sm-1">Rating</th>
                                <th class="text-center col-sm-2">Actions</th>
                            </tr>
                        </thead>


                    </table>
                </div>

            </div>

        </div>
    </div>
</form>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"  style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Change Request</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form1" >

                    <div class="row">                        
                        <input type="hidden" name="class_id" value="" id="class_id">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="temp_num_seats" class="col-sm-4 control-label">Total Seats <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control number_only" id="temp_num_seats" autocomplete="off"  value="" name="temp_num_seats">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="temp_gym_seats" class="col-sm-4 control-label">Gym Seats <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control number_only" id="temp_gym_seats" autocomplete="off"  value="" name="temp_gym_seats" readonly="readonly">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="temp_fitflow_seats" class="col-sm-4 control-label">{{ $appTitle->title }} Seats <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control number_only" id="temp_fitflow_seats" autocomplete="off"  value="" name="temp_fitflow_seats">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="temp_price" class="col-sm-4 control-label">Price <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control number_only" id="temp_price" autocomplete="off"  value="" name="temp_price">
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green btn-icon" id="submit">
                    Save
                    <i class="entypo-check"></i>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
                                            "url":  '{{ url("$configM2/$classMasterID/classes") }}',
                                            complete: function () {
                                            $('.loading-image').hide();
                                            }
                                    },
                                    columns: [
                                    {data: 0, name: 'id', orderable: false, searchable: false, class: 'text-center checkbox_padding'},
                                    {data: 1, name: 'branch', orderable: false},
                                    {data: 2, name: 'num_seats', orderable: false, class: 'text-center'},
                                    {data: 3, name: 'available_seats', orderable: false, class: 'text-center'},
                                    {data: 4, name: 'fitflow_seats', orderable: false, class: 'text-center'},
                                    {data: 5, name: 'status', orderable: false, searchable: false, class: 'text-center'},
                                    {data: 6, name: 'created_at', class: 'text-center'},
                                    {data: 7, name: 'rating', class: 'text-center'},
                                    {data: 9, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                                    ],
                                    order: [[6, 'desc']],
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
                                            "url": '{{ url("$configM2/$classMasterID/classes") }}/' + ID,
                                            data: {id: ID, status: Value, _token: '{{ csrf_token() }}'},
                                            success: function (data) {
                                            $table4.DataTable().ajax.reload(null, false);
                                            toastr.success(data.response, "", opts);
                                            }
                                    });
                                    });
                                    /*------END----*/
                                    /*----sendApproval Email---*/
                                    $('.sendApproval').on('click', function (e) {
                                    e.preventDefault();
                                    var ID = $(this).attr('data-id');
                                    $.ajax({
                                    type: "GET",
                                            async: true,
                                            "url": '{{ url("$configM2")}}/' + ID + '/sendApproval',
                                            data: {id: ID},
                                            success: function (data) {
                                            $table4.DataTable().ajax.reload(null, false);
                                            toastr.success(data.response, "", opts);
                                            }
                                    });
                                    });
                                    /*------END----*/

                                    /*----Change Request---*/
                                    $('#temp_fitflow_seats').on('change', function () {
                                    var fseats = parseFloat($(this).val());
                                    var seats = $('#temp_num_seats').val();
                                    if (fseats > seats){
                                    $('#temp_gym_seats').val(0);
                                    $('#temp_fitflow_seats').val(0);
                                    }
                                    else{
                                    $('#temp_gym_seats').val(parseFloat(seats - fseats));
                                    if (seats == 0) {
                                    $('#temp_gym_seats').val(0);
                                    $('#temp_fitflow_seats').val(0);
                                    }
                                    }
                                    });
                                    $('.changeRequest').on('click', function (e) {
                                    e.preventDefault();
                                    $('#form1')[0].reset();
                                    var ID = $(this).attr('data-id');
                                    $('#class_id').val(ID);
                                    });
                                    $('#submit').on('click', function (e) {
                                    e.preventDefault();
                                    $(this).prop('disabled', true);
                                    var ID = $('#class_id').val();
                                    var temp_num_seats = $('#temp_num_seats').val();
                                    var temp_gym_seats = $('#temp_gym_seats').val();
                                    var temp_fitflow_seats = $('#temp_fitflow_seats').val();
                                    var temp_price = $('#temp_price').val();
                                    $.ajax({
                                    type: "POST",
                                            async: true,
                                            "url": '{{ url("$configM2/classes/changeRequest")}}',
                                            data: {class_id: ID, temp_num_seats: temp_num_seats, temp_gym_seats: temp_gym_seats, temp_fitflow_seats: temp_fitflow_seats, temp_price: temp_price, _token: '{{ csrf_token() }}'},
                                            success: function (data) {
                                            if (data.response) {
                                            $table4.DataTable().ajax.reload(null, false);
                                            toastr.success(data.response, "", opts);
                                            $('#myModal,.modal-backdrop.in').css("display", "none");
                                            }
                                            if (data.error) {
                                            $('#submit').prop('disabled', false);
                                            toastr.error(data.error, "", opts2);
                                            }
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
                            // Sample Toastr Notification
                            var opts2 = {
                            "closeButton": true,
                                    "debug": false,
                                    "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
                                    "toastClass": "error",
                                    "onclick": null,
                                    "showDuration": "300",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "8000",
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
    $('.number_only').keypress(function (e) {
    return isNumbers(e, this);
    });
    function isNumbers(evt, element)
    {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (
            (charCode != 46 || $(element).val().indexOf('.') != - 1) && // “.�? CHECK DOT, AND ONLY ONE.
            (charCode > 57))
            return false;
    return true;
    }

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