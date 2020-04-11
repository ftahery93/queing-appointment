@extends('layouts.master')

@section('title')
Members
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">

<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">

@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url("admin/vendors")  }}">Vendors</a>
</li>
@endsection

@section('pageheading')
Members
@endsection

    <div class="row">
        <div class="col-md-12">
            @include('layouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">
                <div class="row margin10">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <label for="field-2" class="col-sm-4 control-label">Package End Date</label>

                            <div class="col-sm-7">
                                <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Date Range Filter" />
                                <input type="hidden" id="start_date" name="start_date"/>
                                <input type="hidden" id="end_date" name="end_date"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="package" class="col-sm-4 control-label">Packages</label>

                            <div class="col-sm-7">
                                <select name="package" class="select2" data-allow-clear="true" id="package" onchange="GetSelectedTextValue()" >
                                    <option value=" ">--Select Packages</option>
                                    @foreach ($Packages as $Package)
                                    <option value="{{ $Package->name_en }}"> {{ $Package->name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin10">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <label for="member_status" class="col-sm-4 control-label">Member Status</label>

                            <div class="col-sm-7">
                                <select name="member_status" class="select2" data-allow-clear="true" id="member_status" onchange="GetSelectedTextValue()" >
                                    <option value=" ">--Select--</option>    
                                    <option value="1">Expired in 1 Week</option>
                                    <option value="2"> Expired in 2 Weeks</option>
                                    <option value="3"> Expired in 3 Weeks</option>
                                </select>
                            </div>
                        </div>
                          <div class="col-sm-6">
                            <label for="subscription" class="col-sm-4 control-label">Subscription</label>

                            <div class="col-sm-7">
                                <select name="subscription" class="select2" data-allow-clear="true" id="subscription" onchange="GetSelectedTextValue()" >
                                    <option value=" ">--Select--</option>    
                                    <option value="0">New</option>
                                    <option value="1"> Renewed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
              

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="text-center" id="td_checkbox"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>
                                <th class="col-sm-3">Name</th>
                                <th class="col-sm-2">Email</th>
                                <th class="text-center col-sm-1">Mobile</th>
                                <th class="text-center col-sm-1">Gender</th>
                                <th class="col-sm-2">Package</th>
                                <th class="text-center col-sm-2">Period</th>
                                <th class="text-center col-sm-1">Actions</th>
                            </tr>
                        </thead>


                    </table>
                </div>

            </div>

        </div>
    </div>


@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>

<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>


<script type="text/javascript">
                            jQuery(document).ready(function ($) {
                            $('#daterange, #start_date, #end_date').val('');
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
                                            "url":  '{{ url("admin/$vendor_id/members") }}',
                                            data: function (data) {
                                            data.name_en = $('#package').val();
                                            data.start_date = $('#start_date').val();
                                            data.end_date = $('#end_date').val();
                                            data.member_status = $('#member_status').val();
                                            data.subscription = $('#subscription').val();
                                            },
                                            complete: function () {
                                            $('.loading-image').hide();
                                            }
                                    },
                                    columns: [
                                    {data: 0, name: 'id', orderable: false, searchable: false, class: 'text-center checkbox_padding'},
                                    {data: 1, name: 'name'},
                                    {data: 2, name: 'email', class: 'text-center'},
                                    {data: 3, name: 'mobile', class: 'text-center'},
                                    {data: 4, name: 'gender_name', class: 'text-center'},
                                    {data: 5, name: 'package_name', class: 'text-center'},
                                    {data: 7, name: 'end_date', class: 'text-center'},
                                    {data: 9, name: 'action', orderable: false, searchable: false, class: 'text-center'}
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
    });</script>

<script>
    $(function () {
    /*-------Date-----------*/
    $('#sd').datetimepicker({
    format: 'DD/MM/YYYY',
            minDate: new Date(),
            toolbarPlacement: 'bottom',
    }).on('dp.change', function (e) {             
            //Ajax call
            var valueSelected1 = $('#package_id').val();
            var start_date = $(this).val();
            if (valueSelected1 == '') {
                toastr.error('Please choose package', "", opts2);
            }

            $.ajax({
                type: "POST",
                async: true,
                "url": '{{ url("members/getPackageDetail") }}',
                data: {id: valueSelected1, start_date: start_date, _token: '{{ csrf_token() }}'},
                success: function (data) {
                    if (data.error) {
                        toastr.error('Please choose package', "", opts2);
                    }
                    if (data.packages) {
                        $('#ed').val(data.packages.end_date);
                        $('#final_total_amt').val(data.packages.price);
                    }
                }
            });
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
    });
 });

</script>

@endsection