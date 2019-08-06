@extends('vendorLayouts.master')

@section('title')
Class Schedules
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


@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM2.'/classes')  }}">Classes</a>
</li>
@endsection

@section('pageheading')
Class Schedules
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form1" >  

    <div class="row">
        <div class="col-md-12">
            @include('vendorLayouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">


                <div class="panel-heading">

                    <div class="panel-options padding10">
                        @if ($EditAccess==1)
                        <button type="button" class="btn btn-green btn-icon" id="save" onclick="ConfirmDelete();">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        @endif
                        @if ($DeleteAccess==1)
                        <button Onclick="return ConfirmDeleteSchedule();" type="button" class="btn btn-red btn-icon">
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
                                <th class="col-sm-1">ID</th>
                                <th class="col-sm-3">Class Name</th>
                                <th class="text-center col-sm-1">Start Time</th>
                                <th class="text-center col-sm-1">End Time</th>
                                <th class="text-center col-sm-1">Schedule Date</th>
                                <th class="text-center col-sm-1">Total Seats</th>                                
                                <th class="text-center col-sm-1">{{ $appTitle->title }} Seats</th>
                                <th class="text-center col-sm-1">Gym Seats</th>
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
                                            "url":  '{{ url("$configM2/classSchedules/$class_id") }}',
                                            complete: function () {
                                            $('.loading-image').hide();
                                            }
                                    },
                                    columns: [
                                    {data: 0, name: 'id', orderable: false, searchable: false, class: 'text-center checkbox_padding'},
                                    {data: 1, name: 'ID', orderable: false, searchable: false, class: 'text-center checkbox_padding'},
                                    {data: 2, name: 'name_en'},
                                    {data: 3, name: 'start', orderable: false, class: 'text-center'},
                                    {data: 4, name: 'end', orderable: false, class: 'text-center'},
                                    {data: 5, name: 'schedule_date', orderable: false, class: 'text-center'},
                                    {data: 6, name: 'num_seats', orderable: false, class: 'text-center'},
                                    {data: 7, name: 'fitflow_seats', orderable: false, class: 'text-center'},
                                    {data: 8, name: 'gym_seats', orderable: false, class: 'text-center'},
                                    ],
                                    order: [[5, 'desc']],
                                    "fnDrawCallback": function (oSettings) {
                                    $('.number_only').keypress(function (e) {
                                    return isNumbers(e, this);
                                    });
                                    $('.fitflow_seats').on('change', function (e) {
                                    var trid = $(this).closest('tr').attr('id'); // table row ID 
                                    var class_fitflow_seats = parseFloat($(this).attr('data-val'));
                                    var total_seats = parseFloat($(this).attr('total'));
                                    var schedule_fitflow_seats = parseFloat($(this).val());
//                                    if (schedule_fitflow_seats < class_fitflow_seats){
//                                    toastr.error("{{ config('global.lessthanPreviousSeats') }}", "", opts2);
//                                    $(this).val(class_fitflow_seats);
//                                    }
                                    //else 
                                        if (schedule_fitflow_seats > total_seats){
                                    toastr.error("{{ config('global.greaterthanTotalSeats') }}", "", opts2);
                                    $(this).val(class_fitflow_seats);
                                    }
                                    else{
                                    $('tr#' + trid + ' .gym_seats').val(parseFloat(total_seats - schedule_fitflow_seats));
                                    }
                                    });
                                    $('.gym_seats').on('change', function (e) {
                                    var trid = $(this).closest('tr').attr('id'); // table row ID 
                                    var total_seats = parseFloat($(this).attr('total'));
                                    var schedule_gym_seats = parseFloat($(this).val());
                                    if (schedule_gym_seats > total_seats){
                                    toastr.error("{{ config('global.greaterthanTotalSeats') }}", "", opts2);
                                    }
                                    else{
                                    $('tr#' + trid + ' .fitflow_seats').val(parseFloat(total_seats - schedule_gym_seats));
                                    }

                                    });
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
                                    var ID = $('#class_id').val();
                                    var temp_num_seats = $('#temp_num_seats').val();
                                    var temp_gym_seats = $('#temp_gym_seats').val();
                                    var temp_fitflow_seats = $('#temp_fitflow_seats').val();
                                    $.ajax({
                                    type: "POST",
                                            async: true,
                                            "url": '{{ url("$configM2/classes/changeRequest")}}',
                                            data: {class_id: ID, temp_num_seats: temp_num_seats, temp_gym_seats: temp_gym_seats, temp_fitflow_seats: temp_fitflow_seats, _token: '{{ csrf_token() }}'},
                                            success: function (data) {
                                            if (data.response) {
                                            $table4.DataTable().ajax.reload(null, false);
                                            toastr.success(data.response, "", opts);
                                            $('#myModal,.modal-backdrop.in').css("display", "none");
                                            }
                                            if (data.error) {
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

    });
    /*---Confirmation--before--submit-*/
    function ConfirmDelete() {
    var $table4 = $("#table-4");
    var chkId = '';
    $('.check:checked').each(function () {
    chkId = $(this).val();
    });
    if (chkId == '') {
    alert('{{ config('global.deleteCheck') }}');
    return false;
    } else {
    if (confirm('{{ config('global.deleteConfirmation') }}')) {
    var json = [];
    $('#table-4').find('tr').each(function(){
    var id = $(this).attr('id');
    var row = {};
//                                                $(this).find('input').each(function(){
//                                                    row[$(this).attr('name')]=$(this).val();
//                                                });
    var checkdID = $('input:checked', this).val();
    if (checkdID > 0){
    row['ids'] = $('input:checked', this).val();
    row['fitflow_seats'] = $('.fitflow_seats', this).val();
    row['gym_seats'] = $('.gym_seats', this).val();
    row['total_seats'] = $('.total_seats', this).val();
    row['class_fitflow_seats'] = $('.class_fitflow_seats', this).val();
    }

    json[id] = row;
    });
    // var ids = $('.check').serializeArray();
    //var gym_seats = $('.gym_seats').serializeArray();
    $.ajax({
    type: "POST",
            async: true,
            "url": '{{ url("$configM2/classes/classSchedules/edit") }}',
            data: {jsonData: json, _token: '{{ csrf_token() }}'},
            success: function (data) {
            if (data.response) {
            $table4.DataTable().ajax.reload(null, false);
            toastr.success(data.response, "", opts);
            $('#myModal,.modal-backdrop.in').css("display", "none");
            }
            if (data.error) {
            $table4.DataTable().ajax.reload(null, false);
            toastr.error(data.error, "", opts2);
            }
            }
    });
    } else {
    return false;
    }
    }

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
    }
    /*------END----*/
    
       /*---Confirmation--before--Delete-*/
    function ConfirmDeleteSchedule() {
    var $table4 = $("#table-4");
    var chkId = '';
    $('.check:checked').each(function () {
    chkId = $(this).val();
    });
    if (chkId == '') {
    alert('{{ config('global.deleteCheck') }}');
    return false;
    } else {
    if (confirm('{{ config('global.deleteConfirmation') }}')) {
    var json = [];
    $('#table-4').find('tr').each(function(){
    var id = $(this).attr('id');
    var row = {};
//                                                $(this).find('input').each(function(){
//                                                    row[$(this).attr('name')]=$(this).val();
//                                                });
    var checkdID = $('input:checked', this).val();
    if (checkdID > 0){
    row['ids'] = $('input:checked', this).val();
    }

    json[id] = row;
    });
    // var ids = $('.check').serializeArray();
    //var gym_seats = $('.gym_seats').serializeArray();
    $.ajax({
    type: "POST",
            async: true,
            "url": '{{ url("$configM2/classes/classSchedules/delete") }}',
            data: {jsonData: json, _token: '{{ csrf_token() }}'},
            success: function (data) {
            if (data.response) {
            $table4.DataTable().ajax.reload(null, false);
            toastr.success(data.response, "", opts);
            $('#myModal,.modal-backdrop.in').css("display", "none");
            }
            if (data.error) {
            $table4.DataTable().ajax.reload(null, false);
            toastr.error(data.error, "", opts2);
            }
            }
    });
    } else {
    return false;
    }
    }

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
    }
    /*------END----*/

    function isNumbers(evt, element)
    {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (
            (charCode != 46 || $(element).val().indexOf('.') != - 1) && // “.�? CHECK DOT, AND ONLY ONE.
            (charCode > 57))
            return false;
    return true;
    }

</script>


@endsection