@extends('vendorLayouts.master')

@section('title')
Subscribers
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM1.'/instructorSubscriptions') }}">Instructor Subscription</a>
</li>
@endsection

@section('pageheading')
Subscribers
@endsection

<div class="row">
    <div class="col-md-12">
        @include('vendorLayouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-options padding10">
                    <button type="button" class="btn btn-green btn-icon" id="checklist" >
                        <i class="entypo-plus"></i> Add Attendance
                    </button>
                    <a href="#myModal" data-toggle="modal" id="modalclick"></a>
                </div>
            </div>
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="daterange" class="col-sm-4 control-label">Subscribed on</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Date Range Filter" />
                            <input type="hidden" id="start_date" name="start_date"/>
                            <input type="hidden" id="end_date" name="end_date"/>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="log" class="col-sm-4 control-label">Members</label>

                        <div class="col-sm-8">
                            <select name="member" class="select2" data-allow-clear="true" id="member" onchange="GetSelectedTextValue()" >
                                <option value="0">--All--</option>
                                @foreach ($Members as $Member)
                                <option value="{{ $Member->id }}"> {{ $Member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-6 pull-right">
                        <button type="button" class="btn btn-info btn-xs pull-right" style="font-size:15px;">Members: <span id="count" >{{ $Count }}</span> </button>
                    </div>
                </div>
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="text-center" id="td_checkbox"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>
                            <th class="col-sm-3">Subscriber</th>
                            <th class="text-center col-sm-1">Mobile</th> 
                            <th class="text-center col-sm-1">Package</th>                                                               
                            <th class="text-center col-sm-1">Price</th>
                            <th class="text-center col-sm-2">No. Sessions</th>
                            <th class="text-center col-sm-1">Booked</th>
                            <th class="text-center col-sm-2">Subscribed On</th>
<!--                            <th class="text-center col-sm-1">Actions</th>-->
                        </tr>
                    </thead>


                </table>
            </div>

        </div>

    </div>
</div>

<!-- Modal 4(Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Add Attendance</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form2" >

                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="sd" class="col-sm-4 control-label">Start Date <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control datetimepicker" id="sd" autocomplete="off"  value="" name="start_date">
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green btn-icon" id="attendance_submit"  onclick="ConfirmAddAttendance();">
                    Save
                    <i class="entypo-check"></i>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>

<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<script type="text/javascript">
                    jQuery(document).ready(function ($) {
                    //$('#daterange, #start_date, #end_date').val('');
                    var $table4 = jQuery("#table-4");
                    $table4.DataTable({
                    "bStateSave": true,
                            "fnStateSave": function (oSettings, oData) {
                            localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
                            },
                            "fnStateLoad": function (oSettings) {
                            return JSON.parse(localStorage.getItem('DataTables_' + window.location.pathname));
                            },
                            processing: true,
                            serverSide: true,
                            ordering: true,
                            language: {
                            processing: "<img src='{{ asset('assets/images/loader-1.gif') }}'>"
                            },
                            "ajax": {
                            "type": "GET",
                                    "url": '{{ url("$configM1/instructorSubscriptions/$package_id/subscribers") }}',
                                    data: function (data) {
                                    data.start_date = $('#start_date').val();
                                    data.end_date = $('#end_date').val();
                                    data.id = $('#member').val();
                                    },
                                    complete: function () {
                                    $('.loading-image').hide();
                                    }
                            },
                            columns: [
                            {data: 0, name: 'subscription_package_id', orderable: false, searchable: false, class: 'text-center checkbox_padding'},
                            {data: 1, name: 'subscriber'},
                            {data: 2, name: 'mobile', orderable: false, searchable: false, class: 'text-center'},
                            {data: 3, name: 'package_name', class: 'text-center'},
                            {data: 4, name: 'price', orderable: false, searchable: false, class: 'text-center'},
                            {data: 5, name: 'num_points', orderable: false, searchable: false, class: 'text-center'},
                            {data: 6, name: 'num_booked', orderable: false, searchable: false, class: 'text-center'},
                            {data: 7, name: 'created_at', class: 'text-center'},
                            //{data: 8, name: 'action', orderable: false, searchable: false, class: 'text-center'}

                            ],
                            order: [[7, 'desc']],
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
                            $('.subscriber_attendance').on('click', function (e) {
                                    e.preventDefault();
                                    var ID = $(this).attr('data-val');
                                    $('#attendDetails').html('');
                                    $('.loading-image2').show();
                                    $.ajax({
                                        type: "GET",
                                        async: true,
                                        "url": '{{ url("$configM1/instructorSubscriptions/showAttendance") }}/'+ID,
                                        success: function (data) {
                                            $('#attendDetails').html(data.html);
                                        },
                                        complete: function () {
                                            $('.loading-image2').hide();
                                        }
                                    });
                                });
                            },
                            "drawCallback": function (settings) {
                            $('#count').html(settings.json.count);
                            //do whatever  
                            },
                    });
                    });</script>
<script>
    // On change trainer name
    function GetSelectedTextValue() {
    var $table4 = $("#table-4");
    $table4.DataTable().draw();
    }
    $('#daterange').daterangepicker({
    autoUpdateInput: false,
            locale: {
            format: 'DD/MM/YYYY',
            }
    }).on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY') + '  (To)  ' + picker.endDate.format('DD/MM/YYYY'));
    $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
    $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
    GetSelectedTextValue();
    }).on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
    $('#start_date').val('');
    $('#end_date').val('');
    GetSelectedTextValue();
    });
    /*---Confirmation--before--Add Attendance-*/
    function ConfirmAddAttendance() {
        var $table4 = jQuery("#table-4");
    if (confirm('{{ config('global.deleteConfirmation') }}')) {
    var json = [];
    $('#table-4').find('tr').each(function(){
    var id = $(this).attr('id');
    var row = {};
    var checkdID = $('input:checked', this).val();
    if (checkdID > 0){
    row['ids'] = $('input:checked', this).val();   
    }    
    json[id] = row;
    });
     
    var start_date = $('#sd').val();
    
    $.ajax({
    type: "POST",
            async: true,
            "url": '{{ url("$configM1/instructorSubscriptions/subscribers/addAttendance") }}',
            data: {jsonData: json, start_date: start_date, _token: '{{ csrf_token() }}'},
            success: function (data) {
            if (data.response) {
            $table4.DataTable().ajax.reload(null, false);
            toastr.success(data.response, "", opts);
             jQuery.noConflict();
             $('#myModal').modal('hide');
            }
            if (data.error) {
            $table4.DataTable().ajax.reload(null, false);
            toastr.error(data.error, "", opts2);
            }
            }
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
    } else {
    return false;
    }
    }
    /*------END----*/
    $('#checklist').on('click', function (event) {
    event.preventDefault();
    var $table4 = $("#table-4");
    var chkId = '';
    $('.check:checked').each(function () {
    chkId = $(this).val();
    });
    if (chkId == '') {
    alert('{{ config('global.deleteCheck') }}');
    return false;
    } else {
    jQuery.noConflict();
    $('#myModal').modal('show');  }
    
    });

</script> 
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
    $('#sd').datetimepicker({
            format: 'DD/MM/YYYY',
            defaultDate: moment(),
    toolbarPlacement: 'bottom',
    })
    });

</script>

@endsection