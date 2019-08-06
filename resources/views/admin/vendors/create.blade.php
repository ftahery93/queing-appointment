@extends('layouts.master')

@section('title')
Vendors
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/vendors')  }}">Vendors</a>
</li>
@endsection

@section('pageheading')
Vendors
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('/admin/vendors') }}" id="form1" enctype="multipart/form-data">
    {{ method_field('POST') }}
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('layouts.flash-message')
            @yield('form-error')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url('admin/vendors')  }}" class="margin-top0">
                            <button type="button" class="btn btn-red btn-icon">
                                Cancel
                                <i class="entypo-cancel"></i>
                            </button>
                        </a>

                    </div>
                </div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-8">

                            <div class="panel panel-primary" data-collapsed="0">

                                <!-- panel head -->
                                <div class="panel-heading">
                                    <div class="panel-title">Profile Details</div>

                                </div>

                                <!-- panel body -->
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-6{{ $errors->has('name') ? ' has-error' : '' }}">
                                                <label for="name" class="col-sm-4 control-label">Name</label>

                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="name" autocomplete="off" value="{{ old('name') }}" name="name">
                                                    @if ($errors->has('name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                            </div>
                                             <div class="col-sm-6{{ $errors->has('name_ar') ? ' has-error' : '' }}">
                                                <label for="name_ar" class="col-sm-4 control-label">Name(AR)</label>

                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ old('name_ar') }}" name="name_ar">
                                                    @if ($errors->has('name_ar'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('name_ar') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-6{{ $errors->has('password') ? ' has-error' : '' }}">

                                                <label for="password" class="col-sm-4 control-label">Password</label>

                                                <div class="col-sm-8">
                                                    <input type="password" class="form-control" id="password" autocomplete="off"  name="password">
                                                    @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                            </div>

                                            <div class="col-sm-6{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                                <label for="password_confirmation" class="col-sm-4 control-label">Confirm Password</label>

                                                <div class="col-sm-8">
                                                    <input type="password" class="form-control" id="password_confirmation" autocomplete="off"  name="password_confirmation"> 
                                                    @if ($errors->has('password_confirmation'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-6{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                                <label for="mobile" class="col-sm-4 control-label">Mobile</label>

                                                <div class="col-sm-8">
                                                    <input type="tel" class="form-control number_only" id="mobile" autocomplete="off" value="{{ old('mobile') }}" name="mobile">
                                                    @if ($errors->has('mobile'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('mobile') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                             <div class="col-sm-6{{ $errors->has('email') ? ' has-error' : '' }}">
                                                <label for="email" class="col-sm-4 control-label">Email ID</label>

                                                <div class="col-sm-8">
                                                    <input type="email" class="form-control" id="email" autocomplete="off" value="{{ old('email') }}" name="email"> 
                                                    @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                            </div>
                                            

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="col-sm-6{{ $errors->has('modules') ? ' has-error' : '' }}">
                                                <label for="modules" class="col-sm-4 control-label">Modules</label>
                                                <div class="col-sm-8">
                                                    <select name="modules[]" class="select2" data-allow-clear="true" multiple="multiple" >

                                                        @foreach ($modules as $module)
                                                        <option value="{{ $module->id }}" {{ (collect(old('modules'))->contains($module->id)) ? 'selected':'' }} >{{ $module->name_en }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('modules'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('modules') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-sm-6{{ $errors->has('username') ? ' has-error' : '' }}">
                                                <label for="username" class="col-sm-4 control-label">Username</label>

                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="username" autocomplete="off"  value="{{ old('username') }}" name="username">
                                                    @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>


                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="col-sm-12{{ $errors->has('description_en') ? ' has-error' : '' }}">
                                                <label for="description_en" class="col-sm-2 control-label">Description(EN)</label>
                                                <div class="col-sm-10">
                                                    <textarea  class="form-control resize" name="description_en" id="description_en" >{{ old('description_en') }}</textarea>
                                                    @if ($errors->has('description_en'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('description_en') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            
                                            <div class="col-sm-12{{ $errors->has('description_ar') ? ' has-error' : '' }}">
                                                <label for="description_ar" class="col-sm-2 control-label">Description(AR)</label>
                                                <div class="col-sm-10">
                                                    <textarea  class="form-control resize" name="description_ar" id="description_ar" dir="rtl" >{{ old('description_ar') }}</textarea>
                                                    @if ($errors->has('description_ar'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('description_ar') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="col-sm-6{{ $errors->has('code') ? ' has-error' : '' }}">
                                                <label for="code" class="col-sm-4 control-label">Code</label>

                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="code" autocomplete="off"  value="{{ old('code') }}" name="code">
                                                    @if ($errors->has('code'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('code') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="status" class="col-sm-4 control-label">Status</label>

                                                <div class="col-sm-8">
                                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                                        <option value="1" {{ (collect(old('status'))->contains(1)) ? 'selected':'' }}> Active</option>
                                                        <option value="0" {{ (collect(old('status'))->contains(0)) ? 'selected':'' }}> Deactive</option>
                                                    </select>
                                                </div>
                                            </div>


                                        </div>

                                    </div>


                                    <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-12">

                                                <label for="commission" class="col-sm-2 control-label">Admin Commission</label>

                                                <div class="col-sm-8">
                                                    <div class="well">
                                                        <div class="row">
                                                            @foreach ($modules as $module)
                                                             @if ($module->id!=3)
                                                            <div class="col-sm-12  margin-btm10">
                                                                <label for="m{{ $module->id }}" class="col-sm-8 control-label" style="font-size:11px;">{{ ucfirst($module->name_en) }} (%)</label>
                                                                <div class="col-sm-4">
                                                                    <input type="text" class="form-control number_only" id="m{{ $module->id }}" autocomplete="off" name="commission[{{ $module->id }}]" value="{{ old('commission[$module->id]') }}" >
                                                                </div>

                                                            </div>
                                                             @endif
                                                            @endforeach

                                                        </div>

                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                    </div>                       

                                    <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-12">

                                                <label for="profile_image" class="col-sm-2 control-label">Profile Image</label>

                                                <div class="col-sm-10">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput"  id="error_file">
                                                        <div class="fileinput-new thumbnail" style="{{ $vendor_profile_WH }}" data-trigger="fileinput">
                                                            <img src="{{ asset('assets/images/sample-crop-2.png') }}" alt="...">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="{{ $vendor_profile_WH }}"></div>
                                                        <div>
                                                            <span class="btn btn-white btn-file">
                                                                <span class="fileinput-new">Select image</span>
                                                                <span class="fileinput-exists">Change</span>
                                                                <input type="file" name="profile_image" accept="image/*">
                                                            </span>
                                                            <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>

                                                            <p style="margin-top:20px;" ><b> Image Size: {{ $vendor_profile_size }} </b></p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    
                                      <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-12">

                                                <label for="estore_image" class="col-sm-2 control-label">{{ $modules->last()->name_en }}  Image</label>

                                                <div class="col-sm-10">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput"  id="error_file_estore">
                                                        <div class="fileinput-new thumbnail" style="{{ $vendor_estore_WH }}" data-trigger="fileinput">
                                                            <img src="{{ asset('assets/images/sample-crop-2.png') }}" alt="...">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="{{ $vendor_estore_WH }}"></div>
                                                        <div>
                                                            <span class="btn btn-white btn-file">
                                                                <span class="fileinput-new">Select image</span>
                                                                <span class="fileinput-exists">Change</span>
                                                                <input type="file" name="estore_image" accept="image/*">
                                                            </span>
                                                            <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>

                                                            <p style="margin-top:20px;" ><b> Image Size: {{ $vendor_estore_size }} </b></p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="panel panel-primary" data-collapsed="0">

                                <!-- panel head -->
                                <div class="panel-heading">
                                    <div class="panel-title">Bank Details</div>

                                </div>

                                <!-- panel body -->
                                <div class="panel-body">
                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('bank_id') ? ' has-error' : '' }}">
                                            <label for="bank_id" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Bank Name</label>
                                            <div class="col-sm-12">
                                                <select name="bank_id" class="select2" data-allow-clear="true"  >

                                                    @foreach ($banks as $bank)
                                                    <option value="{{ $bank->id }}" {{ (collect(old('bank_id'))->contains($bank->id)) ? 'selected':'' }} >{{ $bank->name_en }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('bank_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('bank_id') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="row margin-btm10">                                       
                                        <div class="col-sm-12{{ $errors->has('acc_name') ? ' has-error' : '' }}">
                                            <label for="acc_name" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Account Name</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="acc_name" autocomplete="off" value="{{ old('acc_name') }}" name="acc_name">
                                                @if ($errors->has('acc_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('acc_name') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('acc_num') ? ' has-error' : '' }}">
                                            <label for="acc_num" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Account Number</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="acc_num" autocomplete="off" value="{{ old('acc_num') }}" name="acc_num">
                                                @if ($errors->has('acc_num'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('acc_num') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('ibn_num') ? ' has-error' : '' }}">
                                            <label for="ibn_num" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">IBAN</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="ibn_num" autocomplete="off" value="{{ old('ibn_num') }}" name="ibn_num">
                                                @if ($errors->has('ibn_num'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('ibn_num') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                </div>

                                <!-- panel head -->
                                <div class="panel-heading">
                                    <div class="panel-title">Contract Details</div>

                                </div>

                                <!-- panel body -->
                                <div class="panel-body">
                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('contract_name') ? ' has-error' : '' }}">
                                            <label for="contract_name" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Contract Name</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="contract_name" autocomplete="off" value="{{ old('contract_name') }}" name="contract_name">
                                                @if ($errors->has('contract_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('contract_name') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('contract_startdate') ? ' has-error' : '' }}">
                                            <label for="contract_startdate" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Start Date</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="contract_startdate" autocomplete="off" value="{{ old('contract_startdate') }}" name="contract_startdate">
                                                @if ($errors->has('contract_startdate'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('contract_startdate') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('contract_enddate') ? ' has-error' : '' }}">
                                            <label for="contract_enddate" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">End Date</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="contract_enddate" autocomplete="off" value="{{ old('contract_enddate') }}" name="contract_enddate">
                                                @if ($errors->has('contract_enddate'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('contract_enddate') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('sale_setting') ? ' has-error' : '' }}">
                                            <label for="acc_name" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Sales Count Date</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="sale_setting" autocomplete="off" value="{{ old('sale_setting') }}" name="sale_setting">
                                                @if ($errors->has('sale_setting'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('sale_setting') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                
                                  <!-- e-store -->
                                <div class="panel-heading">
                                    <div class="panel-title">{{ $modules->last()->name_en }} </div>

                                </div>

                                <!-- panel body -->
                                <div class="panel-body">
                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('delivery_charge') ? ' has-error' : '' }}">
                                            <label for="delivery_charge" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Delivery Charge</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control number_only" id="delivery_charge" autocomplete="off" value="{{ old('delivery_charge') }}" name="delivery_charge">
                                                @if ($errors->has('delivery_charge'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('delivery_charge') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                         <div class="col-sm-12{{ $errors->has('estore_offer_text_en') ? ' has-error' : '' }}">
                                            <label for="estore_offer_text_en" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Offer(EN)</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="estore_offer_text_en" autocomplete="off" value="{{ old('estore_offer_text_en') }}" name="estore_offer_text_en">
                                                @if ($errors->has('estore_offer_text_en'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('estore_offer_text_en') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                         <div class="col-sm-12{{ $errors->has('estore_offer_text_ar') ? ' has-error' : '' }}">
                                            <label for="estore_offer_text_ar" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Offer(AR)</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="estore_offer_text_ar" autocomplete="off" value="{{ old('estore_offer_text_ar') }}" name="estore_offer_text_ar">
                                                @if ($errors->has('estore_offer_text_ar'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('estore_offer_text_ar') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>                                   
                                </div>

                            </div>

                        </div>
                    </div>


                </div>

            </div>

        </div>
    </div>

</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    
     jQuery.validator.addMethod("noSpace", function (value, element) {
        return value.indexOf(" ") < 0 && value != "";
    }, "{{ config('global.spaceValidation') }}");
    
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[a-z0-9\\-]+$/i.test(value);
}, "{{ config('global.alphaNumericValidation') }}");

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
             username: {
                noSpace: true
            },
            code: {
                noSpace: true
            },
            name: "required",
            name_ar: "required",
            description_en: "required",
            description_ar: "required",
            email: "required",
            acc_name: "required",
            bank_id: "required",
            contract_startdate: "required",
            contract_enddate: "required",
            sale_setting: "required",
            acc_num: {
                required: true,
                number: true
            },
            ibn_num: {
                required: true,
                alphanumeric: true
            },
            password: {
                required: true,
                minlength: 6
            },
            civilid: {
                required: true,
                number: true,
                //minlength: 12,
                maxlength: 8
            },
            mobile: {
                required: true,
                number: true,
                //minlength: 8,
                maxlength: 8
            },
            password_confirmation: {
                required: true,
                minlength: 6,
                equalTo: "#password"
            },
            modules: "required",
            commission: {
                required: true,
                currency: true
            },
            profile_image: "required",
           // estore_image: "required",
        },
        errorPlacement: function (error, element) {
            switch (element.attr("name")) {
                case 'profile_image':
                    error.insertAfter($("#error_file"));
                    break;
//                    case 'estore_image':
//                    error.insertAfter($("#error_file_estore"));
//                    break;
                default:
                    error.insertAfter(element);
            }
        }

    });

});

</script>
<script>
    $(function () {
        /*-------Date-----------*/
        $('#sale_setting').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        });

        /*-------Date-----------*/
        $('#contract_startdate').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        }).on('changeDate', function (selected) {
            startDate = new Date(selected.date.valueOf());
            startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
            $('#contract_enddate').datepicker('setStartDate', startDate);
        });

        $('#contract_enddate').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        }).on('changeDate', function (selected) {
            FromEndDate = new Date(selected.date.valueOf());
            FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
            $('#contract_startdate').datepicker('setEndDate', FromEndDate);
        });


    });
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
</script>
@endsection