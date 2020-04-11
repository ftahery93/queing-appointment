@extends('layouts.master')

@section('title')
Subscription Expired
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
Subscription Expired
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">   
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="trainer_id" class="col-sm-4 control-label">Trainers</label>

                        <div class="col-sm-8">
                            <select name="trainer_id" class="select2" data-allow-clear="true" id="trainer_id" onchange="GetSelectedTextValue()" >
                                <option value="0">--Select Trainer</option>
                                @foreach ($Trainers as $Trainer)
                                <option value="{{ $Trainer->id }}"> {{ $Trainer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="daterange" class="col-sm-4 control-label">Expired on</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Date Range Filter" />
                            <input type="hidden" id="start_date" name="start_date"/>
                            <input type="hidden" id="end_date" name="end_date"/>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="log" class="col-sm-4 control-label">Subscriber</label>

                        <div class="col-sm-8">
                            <select name="member" class="select2" data-allow-clear="true" id="member" onchange="GetSelectedTextValue()" >
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
            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <label for="package" class="col-sm-4 control-label">Packages</label>

                        <div class="col-sm-8">
                            <select name="package" class="select2" data-allow-clear="true" id="package" onchange="GetSelectedTextValue()" >
                                <option value=" ">--Select Packages</option>
                                @foreach ($Packages as $Package)
                                <option value="{{ $Package->name_en }}"> {{ $Package->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="gender" class="col-sm-4 control-label">Gender</label>

                        <div class="col-sm-8">
                            <select name="gender_id" class="select2" data-allow-clear="true" id="gender_id" onchange="GetSelectedTextValue()" >
                                <option value=" ">--Select Gender</option>
                                @foreach ($Genders as $Gender)
                                <option value="{{ $Gender->id }}"> {{ $Gender->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-6 pull-right">
                        <button type="button" class="btn btn-info btn-xs pull-right" style="font-size:15px;">Subscriber: <span id="count" >{{ $Count }}</span> </button>
                    </div>
                </div>
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-2">Trainer Name</th>
                            <th class="col-sm-2">Name</th>
                            <th class="col-sm-1">Email</th>
                            <th class="text-center col-sm-1">Mobile</th>
                            <th class="text-center col-sm-1">Gender</th>
                            <th class="col-sm-1">Package Name</th>
                            <th class="text-center col-sm-1">No. of Classes</th>
                            <th class="text-center col-sm-1">Attendance</th>
                            <th class="text-center col-sm-2">Period</th>
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
                                            "url": '{{ url("admin/trainerSubscriptionExpired") }}',
                                            data: function (data) {
                                                data.name_en = $('#package').val();
                                                data.start_date = $('#start_date').val();
                                                data.end_date = $('#end_date').val();
                                                data.id = $('#member').val();
                                                data.trainer_id = $('#trainer_id').val();
                                                data.gender_id = $('#gender_id').val();
                                            },
                                            complete: function () {
                                                $('.loading-image').hide();
                                            }
                                        },
                                        columns: [
                                            {data: 0, name: 'trainer'},
                                            {data: 1, name: 'name'},
                                            {data: 2, name: 'email'},
                                            {data: 3, name: 'mobile', class: 'text-center'},
                                            {data: 4, name: 'gender_name', class: 'text-center'},
                                            {data: 5, name: 'package_name'},
                                            {data: 6, name: 'num_points', class: 'text-center', orderable: false, searchable: false},
                                            {data: 7, name: 'count_class', class: 'text-center', orderable: false, searchable: false},
                                            {data: 9, name: 'period', class: 'text-center', orderable: false, searchable: false}
                                        ],
                                        order: [[8, 'desc']],
                                        "drawCallback": function (settings) {
                                            $('#package').html(settings.json.str);
                                            $('#count').html(settings.json.count);
                                            $('#member').html(settings.json.memberStr);
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
        window.location.href = '{{ url("admin/printTrainerSubscriptionExpired") }}';
    }

    /*------END----*/

    function excelExport() {
        window.location.href = '{{ url("admin/excelTrainerSubscriptionExpired") }}';
    }
</script> 
@endsection