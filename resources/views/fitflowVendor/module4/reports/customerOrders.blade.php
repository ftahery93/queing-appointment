@extends('vendorLayouts.master')

@section('title')
Customer Orders
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
Customer Orders
@endsection

<div class="row">
    <div class="col-md-12">
        @include('vendorLayouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">   
            <div class="row margin10">
                <div class="col-sm-12">

                    <div class="col-sm-4">
                        <label for="customer" class="col-sm-4 control-label">Customer Name</label>

                        <div class="col-sm-8">
                            <input type="text" name="customer" value="" id="customer" class="form-control">                              
                        </div>

                    </div>
                    <div class="col-sm-4">
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


            <div class="row margin10">
                <div class="col-sm-12">

                    <div class="col-sm-6 pull-right">
                        <button type="button" class="btn btn-orange btn-xs pull-right" style="font-size:15px;">Amount: <span id="OrderAmountCount">{{ $OrderAmountCount }}</span> </button>
                        <button type="button" class="btn btn-primary btn-xs pull-right" style="font-size:15px;margin-right:10px;">Orders: <span id="OrderCount">{{ $OrderCount }}</span> </button>
                    </div>
                </div>
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-3">Customer Name</th>
                            <th class="col-sm-2">Email</th>
                            <th class="text-center col-sm-2">Mobile</th>
                            <th class="text-center col-sm-2">No. Orders</th>
                            <th class="col-sm-2 text-right ">Total</th>                           
                            <th class="text-center col-sm-1">Action</th>
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
                                            "url": '{{ url("$configM4/report/customerOrders") }}',
                                            data: function (data) {
                                                data.start_date = $('#start_date').val();
                                                data.end_date = $('#end_date').val();
                                                data.customer_name = $('#customer').val();
                                            },
                                            complete: function () {
                                                $('.loading-image').hide();
                                            }
                                        },
                                        columns: [
                                            {data: 1, name: 'customer_name'},
                                            {data: 2, name: 'email', class: 'text-center', orderable: false},
                                            {data: 3, name: 'mobile', class: 'text-center', orderable: false},
                                            {data: 4, name: 'num_orders', class: 'text-center', orderable: false, searchable: false},
                                            {data: 5, name: 'total', class: 'text-right', orderable: false},
                                            {data: 6, name: 'action', class: 'text-center', orderable: false, searchable: false},
                                        ],
                                        order: [[0, 'DESC']],                                        
                                        "drawCallback": function (settings) {
                                            $('#OrderCount').html(settings.json.OrderCount);
                                            $('#OrderAmountCount').html(settings.json.OrderAmountCount);
                                            //do whatever  
                                        },

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
        window.location.href = '{{ url("$configM4/reportPrint/customerOrders") }}';
    }

    /*------END----*/

    function excelExport() {
        window.location.href = '{{ url("$configM4/excelModule4CustomerOrders") }}';
    }
</script> 
@endsection