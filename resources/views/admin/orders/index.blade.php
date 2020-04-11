@extends('layouts.master')

@section('title')
Orders
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
@if($DeleteAccess!=1)
<style>
    table tr th:first-child, table tr td:first-child{display:none;}
</style>
@endif

@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Orders
@endsection
<form class="form" role="form" method="POST" action="{{ url('admin/orders/delete')  }}" >  
    {{ csrf_field() }} 
    <div class="row">
        <div class="col-md-12">
            @include('layouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">


                <div class="panel-heading">

                    <div class="panel-options">

<!--                        @if ($DeleteAccess==1)
                        <button Onclick="return ConfirmDelete();" type="button" class="btn btn-red btn-icon">
                            Delete
                            <i class="entypo-cancel"></i>
                        </button>
                        @endif-->

                    </div>
                </div>
                <div class="col-sm-12 margin10">                   
                    <div class="col-sm-6">
                        <label for="daterange" class="col-sm-4 control-label">Ordered Date</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Date Range Filter" />
                            <input type="hidden" id="start_date" name="start_date"/>
                            <input type="hidden" id="end_date" name="end_date"/>
                        </div>
                    </div>
                     <div class="col-sm-6">
                        <label for="Vendor_id" class="col-sm-2 control-label">Vendors</label>

                        <div class="col-sm-9">
                            <select name="vendor_id" class="col-sm-12" data-allow-clear="true" style="padding:6px 10px;" id="vendor_id">
                                <option value="0">--Select Vendor</option>
                                @foreach ($Vendors as $Vendor)
                                <option value="{{ $Vendor->id }}"> {{ $Vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                
                <div class="col-sm-12 margin10">

                <div class="col-sm-6">
                    <label for="customer" class="col-sm-4 control-label">Customer Name</label>

                    <div class="col-sm-8">
                        <input type="text" name="customer" value="" id="customer" class="form-control">                              
                    </div>

                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-success btn-small" style="font-size:15px;" onclick="GetSelectedTextValue()">Filter</button>
                    <button type="button" class="btn btn-danger btn-small" style="font-size:15px;" onclick="GetSelectedTextValue2()">Reset</button>
                </div>

            </div>

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
<!--                                <th class="text-center" id="td_checkbox"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>-->
                                <th class="col-sm-1">Order ID</th>
                                <th class="col-sm-1">Vendor</th>
                                <th class="col-sm-2">Customer</th>
                                <th class="col-sm-1">Status</th>
                                <th class="col-sm-1 text-right">Total</th>
                                <th class="col-sm-2">Date Added</th>
                                <th class="col-sm-2">Date Modified</th>
                                <th class="text-center col-sm-1">Actions</th>
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
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
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
                                                 "url":  '@if($customerID!=0) {{ url("admin/orders/$customerID") }}@else{{ url("admin/orders") }}@endif',
                                                data: function (data) {
                                                data.start_date = $('#start_date').val();
                                                data.end_date = $('#end_date').val();
                                                data.vendor_id = $('#vendor_id').val();
                                                 data.customer_name = $('#customer').val();
                                                },
                                                complete: function () {
                                                $('.loading-image').hide();
                                                }
                                        },
                                        columns: [
                                        //{data: 0, name: 'id', orderable: false, searchable: false, class: 'text-center checkbox_padding'},
                                        {data: 0, name: 'order_id'},
                                        {data: 1, name: 'vendor', orderable: false},
                                        {data: 2, name: 'customer', orderable: false},
                                        {data: 3, name: 'status', class: 'text-center', orderable: false, searchable: false},
                                        {data: 4, name: 'total', class: 'text-center', orderable: false, searchable: false},
                                        {data: 5, name: 'created_at', class: 'text-center', orderable: false, searchable: false},
                                        {data: 6, name: 'updated_at', class: 'text-center', orderable: false, searchable: false},
                                        {data: 7, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                                        ],
                                        order: [[0, 'desc']],
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
    function GetSelectedTextValue2() {
        $('input,select').val('');
        $('#vendor_id').val(0).trigger('change');
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
        //GetSelectedTextValue();
    }).on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#start_date').val('');
        $('#end_date').val('');
        //GetSelectedTextValue();
    });
   
</script> 

@endsection