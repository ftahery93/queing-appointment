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

        <div class="panel panel-default" data-collapsed="0">   
            <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
               @if (Auth::user()->hasRolePermission('vendorSubscriptionExpired'))
                <li class="{{ active('admin/vendorSubscriptionExpired') }}">
                    <a href="{{ url('admin/vendorSubscriptionExpired')  }}" >
                        <span class="visible-xs"><i class="entypo-home"></i></span>
                        <span class="hidden-xs">{{ ucwords(str_replace("-", " ", config('global.M1'))) }}</span>
                    </a>
                </li>
                 @endif
                 @if (Auth::user()->hasRolePermission('classSubscriptionExpired'))
                 <li class="{{ active('admin/classSubscriptionExpired') }}">
                    <a href="{{ url('admin/classSubscriptionExpired')  }}" >
                        <span class="visible-xs"><i class="entypo-user"></i></span>
                        <span class="hidden-xs">{{ ucwords(str_replace("-", " ", config('global.M2'))) }}</span>
                    </a>
                </li>
                @endif
                 @if (Auth::user()->hasRolePermission('fitflowMembershipSubscriptionExpired'))
                <li class="{{ active('admin/fitflowMembershipSubscriptionExpired') }}">
                    <a href="{{ url('admin/fitflowMembershipSubscriptionExpired')  }}" >
                        <span class="visible-xs"><i class="entypo-user"></i></span>
                        <span class="hidden-xs">{{ ucwords(str_replace("-", " ", config('global.M3'))) }}</span>
                    </a>
                </li>
                @endif
                 @if (Auth::user()->hasRolePermission('estoreSubscriptionExpired'))
                <li class="{{ active('admin/estoreSubscriptionExpired') }}">
                    <a href="{{ url('admin/estoreSubscriptionExpired')  }}" >
                        <span class="visible-xs"><i class="entypo-user"></i></span>
                        <span class="hidden-xs">{{ ucwords(str_replace("-", " ", config('global.M4'))) }}</span>
                    </a>
                </li>
                @endif
            </ul>
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
                        <label for="daterange" class="col-sm-4 control-label">Expired on</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Date Range Filter" />
                            <input type="hidden" id="start_date" name="start_date"/>
                            <input type="hidden" id="end_date" name="end_date"/>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="member" class="col-sm-4 control-label">Member</label>

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
                    <div class="col-sm-4">
                        <label for="member_type" class="col-sm-4 control-label">Subscribed From</label>

                        <div class="col-sm-8">
                            <select name="member_type" class="select2" data-allow-clear="true" id="member_type" onchange="GetSelectedTextValue()" >
                                <option value=" ">--Select--</option>    
                                <option value="0"> GYM Members</option>
                                <option value="1"> {{ $appTitle->title }} Members</option>

                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6 pull-right">
                        <button type="button" class="btn btn-info btn-xs pull-right" style="font-size:15px;" >Members: <span id="count" >{{ $Count }}</span> </button>
                    </div>
                </div>
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-2">Name</th>
                            <th class="col-sm-2">Email</th>
                            <th class="text-center col-sm-2">Mobile</th>
                            <th class="text-center col-sm-1">Gender</th>
                            <th class="col-sm-3">Package</th>
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
                                            "url": '{{ url("admin/vendorSubscriptionExpired") }}',
                                            data: function (data) {
                                                data.name_en = $('#package').val();
                                                data.start_date = $('#start_date').val();
                                                data.end_date = $('#end_date').val();
                                                data.member_type = $('#member_type').val();
                                                data.id = $('#member').val();
                                                data.vendor_id = $('#vendor_id').val();
                                                data.gender_id = $('#gender_id').val();
                                            },
                                            complete: function () {
                                                $('.loading-image').hide();
                                            }
                                        },
                                        columns: [
                                            {data: 0, name: 'name'},
                                            {data: 1, name: 'email', class: 'text-center'},
                                            {data: 2, name: 'mobile', class: 'text-center'},
                                            {data: 3, name: 'gender_name', class: 'text-center'},
                                            {data: 4, name: 'package_name', class: 'text-center'},
                                            {data: 6, name: 'period', class: 'text-center', orderable: false, searchable: false},
                                        ],
                                        order: [[5, 'desc']],
                                        "drawCallback": function (settings) {
                                            $('#member').html(settings.json.str);
                                            $('#package').html(settings.json.packageStr);
                                            $('#count').html(settings.json.count);
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
        window.location.href = '{{ url("admin/printVendorSubscriptionExpired") }}';
    }

    /*------END----*/

    function excelExport() {
        window.location.href = '{{ url("admin/excelVendorSubscriptionExpired") }}';
    }
</script> 
@endsection