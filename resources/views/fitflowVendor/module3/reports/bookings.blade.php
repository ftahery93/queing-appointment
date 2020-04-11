@extends('vendorLayouts.master')

@section('title')
Bookings
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('content')
@if(!$ViewAccess)
   <h2>Not Allowed to Access this Page</h2>
   @else   
     
@section('breadcrumb')

@endsection

@section('pageheading')
Bookings
@endsection

<div class="row">
    <div class="col-md-12">
        @include('vendorLayouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">   
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="daterange" class="col-sm-4 control-label">Booked Date</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Date Range Filter" />
                            <input type="hidden" id="start_date" name="start_date"/>
                            <input type="hidden" id="end_date" name="end_date"/>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="member" class="col-sm-4 control-label">Classes</label>

                        <div class="col-sm-8">
                            <select name="class_id" class="select2" data-allow-clear="true" id="class_id" onchange="GetSelectedTextValue()" >
                                <option value="0">--All--</option>
                                @foreach ($Classes as $Class)
                                <option value="{{ $Class->id }}"> {{ $Class->name_en }}</option>
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
                        <label for="package" class="col-sm-4 control-label">Subscriber</label>

                        <div class="col-sm-8">
                            <select name="subscriber_id" class="select2" data-allow-clear="true" id="subscriber_id" onchange="GetSelectedTextValue()" >
                                <option value=" ">--Select Subscriber</option>
                                @foreach ($Subscribers as $Subscriber)
                                <option value="{{ $Subscriber->id }}"> {{ $Subscriber->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="member_type" class="col-sm-4 control-label">Schedule Time</label>

                        <div class="col-sm-4">
                            <input type="text" class="form-control datetimepicker" id="start_time" autocomplete="off" name="start_time" >
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control datetimepicker" id="end_time" autocomplete="off" name="end_time" >
                        </div>
                    </div>

                </div>
            </div>
         <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-6 pull-right">
                        <button type="button" class="btn btn-info btn-xs pull-right" style="font-size:15px;margin-left:10px;">Bookings: <span id="Count" >{{ $Count }}</span> </button> 
                        <button type="button" class="btn btn-info btn-xs pull-right" style="font-size:15px;">Total Profit ({{ config('global.amountCurrency') }}) : <span id="TotalProfit" >{{ $TotalProfit }}</span> </button>
                    </div>
                </div>
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-3">Subscriber</th>   
                            <th class="col-sm-2">Class Name</th>                            
                            <th class="col-sm-1 text-center">Start Time</th>
                            <th class="col-sm-1 text-center">End Time</th>                            
                            <th class="col-sm-2 text-center">Schedule Date</th>                                                       
                            <th class="col-sm-1 text-right">Profit ({{ config('global.amountCurrency') }})</th>
                            <th class="col-sm-2 text-center">Created On</th> 
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
<script src="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.js') }}"></script>
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
                                            "url": '{{ url("$configM3/m3/report/bookings") }}',
                                            data: function (data) {
                                                data.start_date = $('#start_date').val();
                                                data.end_date = $('#end_date').val();
                                                data.subscriber_id = $('#subscriber_id').val();
                                                data.class_id = $('#class_id').val();
                                                data.start_time = $('#start_time').val();
                                                data.end_time = $('#end_time').val();
                                            },
                                            complete: function () {
                                                $('.loading-image').hide();
                                            }
                                        },
                                        columns: [
                                            {data: 0, name: 'name'},
                                            {data: 1, name: 'class_name'},
                                            {data: 2, name: 'start', class: 'text-center', orderable: false},
                                            {data: 3, name: 'end', class: 'text-center', orderable: false},
                                            {data: 4, name: 'schedule_date', class: 'text-center', orderable: false},
                                            {data: 5, name: 'amount', class: 'text-right', orderable: false},
                                            {data: 6, name: 'created_at', class: 'text-center'},
                                        ],
                                        order: [[6, 'desc']],
                                        "drawCallback": function(settings) {
                                            $('#Count').html(settings.json.Count);
                                            $('#TotalProfit').html(settings.json.TotalProfit);
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
        window.location.href = '{{ url("$configM3/m3/printBookings") }}';
//        } else {
//            return false;
//        }
    }

    /*------END----*/
    function excelExport() {
        window.location.href = '{{ url("$configM3/m3/excelBookings") }}';
    }
</script> 
<script>
    $(function () {
        /*-------Date-----------*/
        $('#start_time').datetimepicker({
            format: 'hh:00:A',
            toolbarPlacement: 'bottom',
            showClear:true,
        });
    
    $('#end_time').datetimepicker({
            format: 'hh:00:A',
            toolbarPlacement: 'bottom',
            showClear:true,
        }).on('dp.change', function (e) {
            var end_time = $(this).val();
            if(end_time!=''){
           GetSelectedTextValue();
          }
    }).on("dp.hide", function() {
         GetSelectedTextValue();
	});
    });
</script> 
@endif
@endsection