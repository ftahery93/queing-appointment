@extends('layouts.master')

@section('title')
Favourites
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Favourites
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">   
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="daterange" class="col-sm-4 control-label">Date</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Date Range Filter" />
                            <input type="hidden" id="start_date" name="start_date"/>
                            <input type="hidden" id="end_date" name="end_date"/>
                        </div>
                    </div>
                     <div class="col-sm-4">
                        <label for="package" class="col-sm-4 control-label">Vendors</label>

                        <div class="col-sm-8">
                            <select name="vendor_id" class="select2" data-allow-clear="true" id="vendor_id" onchange="GetSelectedTextValue()" >
                                <option value=" ">--Select Vendor</option>
                                @foreach ($Vendors as $Vendor)
                                <option value="{{ $Vendor->id }}"> {{ $Vendor->name }}</option>
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
             <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="subscriber_id" class="col-sm-4 control-label">Subscribers</label>

                        <div class="col-sm-8">
                            <select name="subscriber_id" class="select2" data-allow-clear="true" id="subscriber_id" onchange="GetSelectedTextValue()" >
                                <option value=" ">--Select Subscriber</option>
                                @foreach ($Subscribers as $Subscriber)
                                <option value="{{ $Subscriber->id }}"> {{ $Subscriber->name }}</option>
                                @endforeach
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
                            <th class="col-sm-6">Subscriber</th>
                            <th class="col-sm-6">Vendor</th>
                            <th class="col-sm-2 text-center">Date</th>
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
                                        "url": '{{ url("admin/vendorFavourites") }}',
                                        data: function (data) {
                                            data.start_date = $('#start_date').val();
                                            data.end_date = $('#end_date').val();
                                            data.vendor_id = $('#vendor_id').val();
                                            data.subscriber_id = $('#subscriber_id').val();
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'subscriber', orderable: false},
                                        {data: 1, name: 'vendor', orderable: false},
                                        {data: 2, name: 'created_at', class: 'text-center'}
                                    ],
                                    order: [[1, 'desc']],
                                });

                            });


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
    });
    /*---On Print All Confirmation---*/
    function ConfirmPrint() {          
        //if (confirm('{{ config('global.printConfirmation') }}')) {
            window.location.href = '{{ url("admin/printVendorFavourites") }}';
//        } else {
//            return false;
//        }
    }

    /*------END----*/
    function excelExport(){
    window.location.href = '{{ url("admin/excelVendorFavourites") }}';
     }
</script> 
@endsection