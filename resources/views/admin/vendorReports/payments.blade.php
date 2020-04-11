@extends('layouts.master')

@section('title')
Payments
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Payments
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0"> 
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="vendor_id" class="col-sm-4 control-label">Vendors</label>

                        <div class="col-sm-8">
                            <select name="vendor_id" class="select2" data-allow-clear="true" id="vendor_id" onchange="GetSelectedTextValue()" >
                                <option value="0">--Select Vendor</option>
                                @foreach ($Vendors as $Vendor)
                                <option value="{{ $Vendor->id }}"> {{ $Vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="field-2" class="col-sm-4 control-label">Collected on</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Date Range Filter" />
                            <input type="hidden" id="start_date" name="start_date"/>
                            <input type="hidden" id="end_date" name="end_date"/>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="log" class="col-sm-4 control-label">Member</label>

                        <div class="col-sm-8">
                            <select name="log" class="select2" data-allow-clear="true" id="log" onchange="GetSelectedTextValue()" >
                                <option value="0">--All--</option>
                                @foreach ($Members as $Member)
                                <option value="{{ $Member->id }}"> {{ $Member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        @if ($PrintAccess==1)
                        <div class="col-sm-5 text-right  pull-right">                   
                            <button Onclick="return ConfirmPrint();" type="button" class="btn btn-info btn-icon">
                                Print Preview
                                <i class="entypo-print"></i>
                            </button>                      
                        </div>
                        <div class="col-sm-5 text-right  pull-right paddingRight0">                     
                            <button type="button" class="btn btn-success btn-icon" Onclick="return excelExport();">
                                Excel Export
                                <i class="entypo-export"></i>
                            </button>                     
                        </div>
                        @endif
                    </div>

                </div>
            </div>

            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-2 text-center">Invoice No.</th>
                            <th class="col-sm-3">Name</th>
                            <th class="col-sm-3">Package Name</th>
                            <th class="col-sm-1 text-center">Collected On</th>
                            <th class="col-sm-1">Collected By</th>                          
                            <th class="col-sm-1 text-right">Cash {{ config('global.amountCurrency') }}</th>
                            <th class="col-sm-1 text-right">KNET {{ config('global.amountCurrency') }}</th>
                            <th class="col-sm-1 text-right">Fee {{ config('global.amountCurrency') }}</th>
                        </tr>
                    </thead>


                </table>
                
                <div class="row">
                    <div class="col-sm-8 pull-right">
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th><div class="pull-left"><h4><b>Total Amount ({{ config('global.amountCurrency') }})</b><h4></div> <div class="pull-right"><h4><b id="amount">{{ $invoiceAmount->fees }}</b><h4></div></th>
                                                                <th><div class="pull-left"><h4><b>Total Cash ({{ config('global.amountCurrency') }})</b><h4></div> <div class="pull-right"> <h4><b id="cc_amount">{{ $invoiceAmount->cash_amount }}</b><h4></div></th>
                                                                                            <th><div class="pull-left"><h4><b>Total KNET ({{ config('global.amountCurrency') }})</b><h4> </div> <div class="pull-right"><h4><b id="knet_amount">{{ $invoiceAmount->knet_amount }}</b><h4></div></th>
                                                                                                                        </tr>

                                                                                                                        </thead>
                                                                                                                        </table>  
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
                                jQuery(document).ready(function ($) {
                                    //$('#daterange, #start_date, #end_date').val('');
                                    var $table4 = jQuery("#table-4");
                                    $table4.DataTable({
                                        //"stateSave": true,
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
                                            "url": '{{ url("admin/vendorPayments") }}',
                                            data: function (data) {
                                                data.start_date = $('#start_date').val();
                                                data.end_date = $('#end_date').val();
                                                data.id = $('#log').val();
                                                data.vendor_id = $('#vendor_id').val();
                                            },
                                            complete: function () {
                                                $('.loading-image').hide();
                                            }
                                        },
                                        columns: [
                                            {data: 0, name: 'receipt_num', orderable: false, class: 'text-center'},
                                            {data: 1, name: 'name'},
                                            {data: 2, name: 'package_name'},
                                            {data: 3, name: 'created_at', class: 'text-center'},
                                            {data: 4, name: 'collected_by'},
                                            {data: 5, name: 'cash', class: 'text-right',orderable: false},
                                            {data: 6, name: 'knet', class: 'text-right',orderable: false},
                                            {data: 7, name: 'price', class: 'text-right',orderable: false},
                                        ],
                                        order: [[3, 'desc']],
                                         "drawCallback": function(settings) {
                                            //console.log(settings.json.Amount.fees);
                                            $('#amount').html(settings.json.invoiceAmount.fees);
                                            $('#cc_amount').html(settings.json.invoiceAmount.cash_amount);
                                            $('#knet_amount').html(settings.json.invoiceAmount.knet_amount);
                                            $('#log').html(settings.json.str); 
//                                            var newOption = new Option(settings.json.Members.name, settings.json.Members.id, false, false);
//                                            $('#log').append(newOption).trigger('change');
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
    /*---On Print All Confirmation---*/
    function ConfirmPrint() {
        window.location.href = '{{ url("admin/printVendorPayments") }}';
    }

    /*------END----*/

    function excelExport() {
        window.location.href = '{{ url("admin/excelVendorPayments") }}';
    }
</script> 
@endsection