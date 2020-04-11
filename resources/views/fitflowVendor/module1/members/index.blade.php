@extends('vendorLayouts.master')

@section('title')
Members
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">

<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@if($EditAccess!=1)
<style>
    table tr th:last-child, table tr td:last-child{display:none;}
</style>
@endif
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
Members
@endsection
<form class="form" role="form" method="POST" action="{{ url($configM1.'/members/delete')  }}" >  
    {{ csrf_field() }} 
    <div class="row">
        <div class="col-md-12">
            @include('vendorLayouts.flash-message')

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
                                <select name="package"  class="col-sm-12" data-allow-clear="true" id="package" style="padding:6px 10px;" onchange="GetSelectedTextValue()" >
                                    <option value="">--Select Packages</option>
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
                                <select name="member_status" class="col-sm-12" data-allow-clear="true" id="member_status" style="padding:6px 10px;" onchange="GetSelectedTextValue()" >
                                    <option value="">--Select--</option>    
                                    <option value="1">Expired in 1 Week</option>
                                    <option value="2"> Expired in 2 Weeks</option>
                                    <option value="3"> Expired in 3 Weeks</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="member_type" class="col-sm-4 control-label">Subscribed From</label>

                            <div class="col-sm-7">
                                <select name="member_type" class="col-sm-12" data-allow-clear="true" id="member_type" style="padding:6px 10px;" onchange="GetSelectedTextValue()" >
                                    <option value="">--Select--</option>    
                                    <option value="0"> GYM Members</option>
                                    <option value="1"> {{ $appTitle->title }} Members</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin10">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <label for="subscription" class="col-sm-4 control-label">Subscription</label>

                            <div class="col-sm-7">
                                <select name="subscription" class="col-sm-12" data-allow-clear="true" id="subscription" style="padding:6px 10px;" onchange="GetSelectedTextValue()" >
                                    <option value="">--Select--</option>    
                                    <option value="0">New</option>
                                    <option value="1"> Renewed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="gender" class="col-sm-4 control-label">Gender</label>

                            <div class="col-sm-7">
                                <select name="gender_id" class="col-sm-12" data-allow-clear="true" id="gender_id" style="padding:6px 10px;" onchange="GetSelectedTextValue()" >
                                    <option value="">--Select Gender</option>
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
                            <button type="button" class="btn btn-danger btn-icon pull-right" id="resetFilter">Reset Filter <i class="entypo-ccw"></i></button>
                        </div>
                    </div>

                </div>


                <div class="panel-heading margin10">

                    <div class="panel-options">  
                        @if ($CreateAccess==1)
                        <a href="{{ url($configM1.'/members/create')  }}" class="margin-top0">
                            <button type="button" class="btn btn-default btn-icon" id="addRecord">
                                Add Record
                                <i class="entypo-plus padding10"></i>
                            </button>
                        </a>
                        @endif
                        @if ($DeleteAccess==1)
                        <button Onclick="return ConfirmDelete();" type="button" class="btn btn-red btn-icon" id="deleteRecord">
                            Delete
                            <i class="entypo-cancel"></i>
                        </button>
                        <a href="{{ url($configM1.'/members/trashedlist')  }}" class="margin-top0">
                            <button type="button" class="btn btn-orange btn-icon" id="trashList">
                                Trash List
                                <i class="entypo-ccw padding10"></i>
                            </button>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="text-center" id="td_checkbox"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>
                                <th class="col-sm-1">Name</th>
                                <th class="col-sm-1">Email</th>
                                <th class="text-center col-sm-1">Mobile</th>
                                <th class="text-center col-sm-1">Gender</th>
                                <th class="col-sm-1">Package</th>
                                <th class="text-center col-sm-2">Period</th>
                                <th class="text-center col-sm-1">Status</th>
                                <th class="text-center col-sm-3">Actions</th>
                            </tr>
                        </thead>


                    </table>
                </div>

            </div>

        </div>
    </div>
</form>

<!-- Modal 3(Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Renew Package</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form1" >


                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="package_id" class="col-sm-4 control-label">Packages <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <select name="package_id" class="col-sm-12" data-allow-clear="true" id="package_id" style="padding:6px 10px;">
                                        <option value="0">--Select--</option>
                                        @foreach ($Packages as $package)
                                        <option value="{{ $package->id }}" >{{ $package->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">                        
                        <input type="hidden" name="member_id" value="" id="member_id">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="sd" class="col-sm-4 control-label">Start Date <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control datetimepicker" id="sd" autocomplete="off"  value="" name="start_date">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-6">
                                <label for="cash" class="col-sm-5 control-label">Cash <span style="color:red;">*</span></label>

                                <div class="col-sm-7">  
                                    <input type="text" class="form-control" id="cash" autocomplete="off"  value="{{ old('cash') }}" name="cash"
                                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 8 || event.charCode == 46">

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="knet" class="col-sm-5 control-label">KNET</label>

                                <div class="col-sm-7"> 
                                    <input type="text" class="form-control" id="knet" autocomplete="off"  value="{{ old('knet') }}" name="knet"
                                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 8 || event.charCode == 46">

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label for="end_date" class="col-sm-5 control-label">End Date</label>

                                <div class="col-sm-7">
                                    <input type="end_date" class="form-control" id="ed" autocomplete="off" disabled="disabled">                                    
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="final_total_amt" class="col-sm-5 control-label">Fee {{ config('global.amountCurrency') }}</label>

                                <div class="col-sm-7">
                                    <input type="final_total_amt" class="form-control" id="final_total_amt" value="0" autocomplete="off" disabled="disabled">                                    
                                </div>
                            </div>

                        </div>

                    </div>


                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green btn-icon" id="submit">
                    Save
                    <i class="entypo-check"></i>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal 2 (Invoice)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content"  id="invoice">


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal 4(Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="instructorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Instructor Subscription</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form2" >
                   <input type="hidden" name="instructor_member_id" value="" id="instructor_member_id">
                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="instructor_package_id" class="col-sm-4 control-label">Packages <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <select name="instructor_package_id" class="col-sm-12" data-allow-clear="true" id="instructor_package_id" style="padding:6px 10px;">
                                        <option value="0">--Select--</option>
                                        @foreach ($instructorPackages as $package)
                                        <option value="{{ $package->id }}" data-price="{{ $package->price }}" >{{ $package->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="instructor_price" class="col-sm-4 control-label">Fee {{ config('global.amountCurrency') }}</label>

                                <div class="col-sm-8">
                                    <input type="instructor_price" class="form-control" id="instructor_price" value="0" autocomplete="off" disabled="disabled">                                    
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-6">
                                <label for="instructor_cash" class="col-sm-5 control-label">Cash <span style="color:red;">*</span></label>

                                <div class="col-sm-7">  
                                    <input type="text" class="form-control" id="instructor_cash" autocomplete="off"  value="{{ old('instructor_cash') }}" name="instructor_cash"
                                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 8 || event.charCode == 46">

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="instructor_knet" class="col-sm-5 control-label">KNET</label>

                                <div class="col-sm-7"> 
                                    <input type="text" class="form-control" id="instructor_knet" autocomplete="off"  value="{{ old('instructor_knet') }}" name="instructor_knet"
                                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 8 || event.charCode == 46">

                                </div>
                            </div>
                        </div>

                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green btn-icon" id="instructor_submit">
                    Save
                    <i class="entypo-check"></i>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal 4 (Instructor Invoice)-->
<!-- Modal -->
<div class="modal fade" id="instructorInvoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content"  id="instructorInvoiceHtml">


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

                                               $('#resetFilter').on('click', function (e) {
                                               localStorage.clear();
                                               $('input,select').val('');
                                               GetSelectedTextValue();
                                               });
                                               $('.edit').on('click', function (e) {
                                               localStorage.setItem('memberFilterStorage', 2);
                                               });
                                               //Storage for filter records 1:Empty, 2:NotEmpty
                                               if (localStorage.getItem("memberFilterStorage") === null) {
                                               localStorage.setItem('memberFilterStorage', 1);
                                               }

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
                                                               "url":  '{{ url("$configM1/members") }}',
                                                               data: function (data) {
                                                               //Set Local Storage key value 
                                                               if (localStorage.getItem('memberFilterStorage') == 1){
                                                               localStorage.setItem('name_en', $('#package').val());
                                                               localStorage.setItem('start_date', $('#start_date').val());
                                                               localStorage.setItem('end_date', $('#end_date').val());
                                                               localStorage.setItem('member_type', $('#member_type').val());
                                                               localStorage.setItem('member_status', $('#member_status').val());
                                                               localStorage.setItem('subscription', $('#subscription').val());
                                                               localStorage.setItem('gender_id', $('#gender_id').val());
                                                               }

                                                               data.name_en = localStorage.getItem('name_en');
                                                               data.start_date = localStorage.getItem('start_date');
                                                               data.end_date = localStorage.getItem('end_date');
                                                               data.member_type = localStorage.getItem('member_type');
                                                               data.member_status = localStorage.getItem('member_status');
                                                               data.subscription = localStorage.getItem('subscription');
                                                               data.gender_id = localStorage.getItem('gender_id');
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
                                                       {data: 4, name: 'gender_name', class: 'text-center', orderable: false},
                                                       {data: 5, name: 'package_name', class: 'text-center', orderable: false},
                                                       {data: 7, name: 'end_date', class: 'text-center', orderable: false},
                                                       {data: 8, name: 'status', orderable: false, searchable: false, class: 'text-center'},
                                                       {data: 14, name: 'action', orderable: false, searchable: false, class: 'text-center'}
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
                                                       /*----Add Renew Package---*/
                                                       $('.renew_package').on('click', function (e) {
                                                       e.preventDefault();
                                                       $('#form1')[0].reset();
                                                       var ID = $(this).attr('data-val');
                                                       var edate = $(this).attr('enddate');
                                                       $('#member_id').val(ID);
                                                       $('#sd').val(edate);
                                                       });
                                                       $('#submit').on('click', function (e) {
                                                       e.preventDefault();
                                                       $(this).prop('disabled', true);
                                                       var ID = $('#member_id').val();
                                                       var start_date = $('#sd').val();
                                                       var package_id = $('#package_id').val();
                                                       var cash = $('#cash').val();
                                                       var knet = $('#knet').val();
                                                       var payment_method = $('#payment_method').val();
                                                       $.ajax({
                                                       type: "POST",
                                                               async: true,
                                                               "url":  '{{ url("$configM1/members/renewPackage") }}',
                                                               data: {id: ID, start_date: start_date, package_id: package_id, cash: cash, knet: knet, _token: '{{ csrf_token() }}'},
                                                               success: function (data) {
                                                               if (data.response) {
                                                               $table4.DataTable().ajax.reload(null, false);
                                                               toastr.success(data.response, "", opts);
                                                               $('#myModal2,.modal-backdrop.in').css("display", "none");
                                                               }
                                                               if (data.error) {
                                                               $('#submit').prop('disabled', false);
                                                               toastr.error(data.error, "", opts2);
                                                               }
                                                               }
                                                       });
                                                       });
                                                       /*------END----*/
                                                       /*----Payment Details---*/
                                                       $('.member_invoice').on('click', function (e) {
                                                       e.preventDefault();
                                                       $('#invoice').html('');
                                                       var member_id = $(this).attr('data-val');
                                                       $('.loading-image').show();
                                                       $.ajax({
                                                       type: "GET",
                                                               async: true,
                                                               "url": '{{ url("$configM1")}}/' + member_id + '/invoice',
                                                               success: function (data) {
                                                               $('#invoice').html(data.html);
                                                               },
                                                               complete: function () {
                                                               $('.loading-image').hide();
                                                               }
                                                       });
                                                       });
                                                       /*------END----*/
                                                       /*----sendInvoice Email---*/
                                                       $('.sendInvoice').on('click', function (e) {
                                                       e.preventDefault();
                                                       var ID = $(this).attr('data-id');
                                                       $.ajax({
                                                       type: "GET",
                                                               async: true,
                                                               "url": '{{ url("$configM1")}}/' + ID + '/sendInvoice',
                                                               data: {id: ID},
                                                               success: function (data) {
                                                               $table4.DataTable().ajax.reload(null, false);
                                                               toastr.success(data.response, "", opts);
                                                               }
                                                       });
                                                       });
                                                       /*------END----*/
                                                       /*----Status Update---*/
                                                       $('.status').on('click', function (e) {
                                                       e.preventDefault();
                                                       var ID = $(this).attr('sid');
                                                       var Value = $(this).attr('value');
                                                       $.ajax({
                                                       type: "PATCH",
                                                               async: true,
                                                               "url": '{{ url("$configM1/members") }}/' + ID,
                                                               data: {id: ID, status: Value, _token: '{{ csrf_token() }}'},
                                                               success: function (data) {
                                                               $table4.DataTable().ajax.reload(null, false);
                                                               toastr.success(data.response, "", opts);
                                                               }
                                                       });
                                                       });
                                                       /*------END----*/
                                                        /*----Add Instructor Package---*/
                                                       $('.instructor_package').on('click', function (e) {
                                                       e.preventDefault();
                                                       $('#form2')[0].reset();
                                                       var ID = $(this).attr('data-val');
                                                       console.log(ID);
                                                       $('#instructor_member_id').val(ID);
                                                       });
                                                       $('#instructor_submit').on('click', function (e) {
                                                       e.preventDefault();
                                                       $(this).prop('disabled', true);
                                                       var ID = $('#instructor_member_id').val();
                                                       var package_id = $('#instructor_package_id').val();
                                                       var cash = $('#instructor_cash').val();
                                                       var knet = $('#instructor_knet').val();
                                                       $.ajax({
                                                       type: "POST",
                                                               async: true,
                                                               "url":  '{{ url("$configM1/members/instructorSubscription") }}',
                                                               data: {id: ID,  package_id: package_id, cash: cash, knet: knet, _token: '{{ csrf_token() }}'},
                                                               success: function (data) {
                                                               if (data.response) {
                                                               $table4.DataTable().ajax.reload(null, false);
                                                               toastr.success(data.response, "", opts);
                                                               $('#instructorModal,.modal-backdrop.in').css("display", "none");
                                                               }
                                                               if (data.error) {
                                                               $('#instructor_submit').prop('disabled', false);
                                                               toastr.error(data.error, "", opts2);
                                                               }
                                                               }
                                                       });
                                                       });
                                                       /*------END----*/
                                                       /*----Instructor Payment Details---*/
                                                       $('.instructor_invoice').on('click', function (e) {
                                                       e.preventDefault();
                                                       $('#instructorInvoiceHtml').html('');
                                                       var member_id = $(this).attr('data-val');
                                                       $('.loading-image').show();
                                                       $.ajax({
                                                       type: "GET",
                                                               async: true,
                                                               "url": '{{ url("$configM1")}}/' + member_id + '/instructorInvoice',
                                                               success: function (data) {
                                                               $('#instructorInvoiceHtml').html(data.html);
                                                               },
                                                               complete: function () {
                                                               $('.loading-image').hide();
                                                               }
                                                       });
                                                       });
                                                       /*------END----*/
                                                       $('.edit, #addRecord,#deleteRecord,#trashList').on('click', function (e) {
                                                       localStorage.setItem('memberFilterStorage', 2);
                                                       });
                                                       //Set Input value if filter record not empty
                                                       if (localStorage.getItem("memberFilterStorage") == 2) {
                                                       if (localStorage.getItem('start_date') != '' && localStorage.getItem('end_date') != ''){
                                                       var formattedDate = new Date(localStorage.getItem('start_date'));
                                                       var d = formattedDate.getDate();
                                                       var m = formattedDate.getMonth();
                                                       m += 1; // JavaScript months are 0-11
                                                       var y = formattedDate.getFullYear();
                                                       var formattedDate2 = new Date(localStorage.getItem('end_date'));
                                                       var d2 = formattedDate2.getDate();
                                                       var m2 = formattedDate2.getMonth();
                                                       m2 += 1; // JavaScript months are 0-11
                                                       var y2 = formattedDate2.getFullYear();
                                                       var sDate = d + "/" + m + "/" + y;
                                                       var eDate = d2 + "/" + m2 + "/" + y2;
                                                       $('#daterange').val(sDate + ' (To) ' + eDate);
                                                       }
                                                       if (localStorage.getItem('name_en') != '')
                                                               $('#package').val(localStorage.getItem('name_en'));
                                                       if (localStorage.getItem('member_type') != '')
                                                               $('#member_type').val(localStorage.getItem('member_type'));
                                                       if (localStorage.getItem('member_status') != '')
                                                               $('#member_status').val(localStorage.getItem('member_status'));
                                                       if (localStorage.getItem('subscription') != '')
                                                               $('#subscription').val(localStorage.getItem('subscription'));
                                                       if (localStorage.getItem('gender_id') != '')
                                                               $('#gender_id').val(localStorage.getItem('gender_id'));
                                                       }
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
    // On change 
    function GetSelectedTextValue() {
    var $table4 = $("#table-4");
    $table4.DataTable().draw();
    localStorage.setItem('memberFilterStorage', 1);
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
    if (valueSelected1 == 0) {
    $(this).val('');
    toastr.error('Please choose package', "", opts2);
    }

    $.ajax({
    type: "POST",
            async: true,
            "url": '{{ url("$configM1/members/getPackageDetail") }}',
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
    });</script>
<script>
    //on change cash
    $(document).on('change', '#cash', function () {
    var cash_amt = parseFloat($(this).val()).toFixed(3);
    var amt = $('#final_total_amt').val();
    var final_total_amt = parseFloat($('#final_total_amt').val()).toFixed(3);
    $('#knet').val(parseFloat(final_total_amt - cash_amt).toFixed(3));
    if (amt == 0) {
    $('#knet').val(parseFloat(0).toFixed(3));
    $('#cash').val(parseFloat(0).toFixed(3));
    }
    });
    //on change knet
    $(document).on('change', '#knet', function () {
    //var str=$(this).attr('id');
    // var id=str.split("_",1);
    var cash_amt = parseFloat($(this).val()).toFixed(3);
    var amt = $('#final_total_amt').val();
    var final_total_amt = parseFloat($('#final_total_amt').val()).toFixed(3);
    $('#cash').val(parseFloat(final_total_amt - cash_amt).toFixed(3));
    if (amt == 0) {
    $('#knet').val(parseFloat(0).toFixed(3));
    $('#cash').val(parseFloat(0).toFixed(3));
    }
    });
    $(document).on('change', '#package_id', function () {
    var start_date = $('#sd').val();
    var valueSelected1 = $('#package_id').val();
    if (start_date != ''){
    $.ajax({
    type: "POST",
            async: true,
            "url": '{{ url("$configM1/members/getPackageDetail") }}',
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
    }
    });
    
     //on change Package
    $(document).on('change', '#instructor_package_id', function () {
       var price = $("option:selected", this).attr('data-price');
       var valueSelected1 = $('#instructor_package_id').val();
       $('#instructor_price').val(price);
    });
    
    //on change Instructor cash
    $(document).on('change', '#instructor_cash', function () {
    var cash_amt = parseFloat($(this).val()).toFixed(3);
    var amt = $('#instructor_price').val();
    var final_total_amt = parseFloat($('#instructor_price').val()).toFixed(3);
    $('#instructor_knet').val(parseFloat(final_total_amt - cash_amt).toFixed(3));
    if (amt == 0) {
    $('#instructor_knet').val(parseFloat(0).toFixed(3));
    $('#instructor_cash').val(parseFloat(0).toFixed(3));
    }
    });
    //on change Instructor knet
    $(document).on('change', '#instructor_knet', function () {
    //var str=$(this).attr('id');
    // var id=str.split("_",1);
    var cash_amt = parseFloat($(this).val()).toFixed(3);
    var amt = $('#instructor_price').val();
    var final_total_amt = parseFloat($('#instructor_price').val()).toFixed(3);
    $('#instructor_cash').val(parseFloat(final_total_amt - cash_amt).toFixed(3));
    if (amt == 0) {
    $('#instructor_knet').val(parseFloat(0).toFixed(3));
    $('#instructor_cash').val(parseFloat(0).toFixed(3));
    }
    });

</script>
@endsection