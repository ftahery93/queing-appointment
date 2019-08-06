@extends('layouts.master')

@section('title')
Products Purchased
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
Products Purchased
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">   
            <div class="row margin10">
                <div class="col-sm-12">

                    <div class="col-sm-3">
                        <label for="product_name" class="col-sm-4 control-label">Product Name</label>

                        <div class="col-sm-8">
                            <input type="text" name="product_name" value="" id="product_name" class="form-control">                              
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <label for="vendor_id" class="col-sm-4 control-label">Vendors </label>

                        <div class="col-sm-8">
                            <select name="vendor_id" class="col-sm-12" data-allow-clear="true" style="padding:6px 10px;" id="vendor_id">
                                <option value="0">--Select Vendor</option>
                                @foreach ($Vendors as $Vendor)
                                <option value="{{ $Vendor->id }}"> {{ $Vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-success btn-small" style="font-size:15px;" onclick="GetSelectedTextValue()">Filter</button>
                        <button type="button" class="btn btn-danger btn-small" style="font-size:15px;" onclick="GetSelectedTextValue2()">Reset</button>
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
                            <th class="col-sm-2">Vendor</th>
                            <th class="col-sm-4">Product Name</th>
                            <th class="col-sm-1">Model</th>
                            <th class="text-center col-sm-2">Quantity</th>
                            <th class="col-sm-2 text-right ">Total</th> 
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
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
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
                                            "url": '{{ url("admin/orderReport/productPurchased") }}',
                                            data: function (data) {
                                                data.start_date = $('#start_date').val();
                                                data.end_date = $('#end_date').val();
                                                data.product_name = $('#product_name').val();
                                                data.vendor_id = $('#vendor_id').val();
                                            },
                                            complete: function () {
                                                $('.loading-image').hide();
                                            }
                                        },
                                        columns: [
                                            {data: 0, name: 'vendor'},
                                            {data: 1, name: 'product_name'},
                                            {data: 2, name: 'model', class: 'text-center', orderable: false},
                                            {data: 3, name: 'quantity', class: 'text-center', orderable: false},
                                            {data: 4, name: 'total', class: 'text-right', orderable: false},
                                        ],
                                        order: [[3, 'DESC']],
                                    });

                                });


</script>
<script>
    // On change trainer name
    function GetSelectedTextValue() {
        var $table4 = $("#table-4");
        $table4.DataTable().draw();
    }
    function GetSelectedTextValue2() {
        $('input').val('');
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
        GetSelectedTextValue();
    }).on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#start_date').val('');
        $('#end_date').val('');
        GetSelectedTextValue();
    });
    /*---On Delete All Confirmation---*/
    function ConfirmPrint() {
        if (confirm('{{ config('global.printConfirmation') }}')) {
            return true;
        } else {
            return false;
        }
    }

    /*------END----*/
    /*---On Print All Confirmation---*/
    function ConfirmPrint() {
        window.location.href = '{{ url("admin/orderReportPrint/productPurchased") }}';
    }

    /*------END----*/

    function excelExport() {
        window.location.href = '{{ url("admin/excelModule4ProductPurchased") }}';
    }
</script> 
@endsection