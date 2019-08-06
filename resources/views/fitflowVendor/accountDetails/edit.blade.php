@extends('vendorLayouts.master')

@section('title')
Settings
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Settings
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url($configName.'/user/info')  }}" method="POST" id="form1" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="PUT">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('vendorLayouts.flash-message')
            @yield('form-error')
            @endif

            @if(Session::has('error'))
            @include('vendorLayouts.flash-message')
            @endif

            @if(Session::has('message'))
            @include('vendorLayouts.flash-message')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url($configName.'/home') }}" class="margin-top0">
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
                                    <div class="panel-title">Profile Image</div>

                                </div>

                                <div class="panel-body">

                                    <div class="row">
                                        <div class="form-group col-sm-12" >

                                            <div class="col-sm-12" @if($Vendor->profile_image != '' || $Vendor->profile_image!=null) style="display:none;" @endif id="upload_image">

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
                                                                <input type="file" name="profile_image" accept="image/*" id="profile_image">
                                                                <input type="hidden" name="uploaded_image_removed" id="uploaded_image_removed" value="0">
                                                            </span>
                                                            <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                            <p style="margin-top:20px;" ><b> Image Size: {{ $vendor_profile_size }} </b></p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            @if($Vendor->profile_image)
                                            <div class="col-sm-12"  id="uploaded_image">

                                                <label for="profile_image" class="col-sm-2 control-label">Uploaded Profile Image</label>

                                                <div class="col-sm-10">
                                                    <img src="{{ url('public/vendors_images/'.$Vendor->profile_image) }}" alt="Uploaded Profile Image" style="{{ $vendor_profile_WH }}">

                                                    <div class="col-sm-12" style="margin-top:20px;">
                                                        <a href="javascript:void(0);" class="btn btn-orange fileinput-exists" data-dismiss="fileinput" id="remove_image">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                        </div>

                                    </div>

                                    @if(str_contains($Vendor->modules, '4'))                                    
                                    <div class="row">
                                        <div class="form-group col-sm-12" >

                                            <div class="col-sm-12" @if($Vendor->estore_image != '' || $Vendor->estore_image!=null) style="display:none;" @endif id="upload_image_estore">

                                                 <label for="estore_image" class="col-sm-2 control-label">{{ $module_name }} Image</label>

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
                                                                <input type="file" name="estore_image" accept="image/*" id="profile_image_estore">
                                                                <input type="hidden" name="uploaded_image_removed_estore" id="uploaded_image_removed_estore" value="0">
                                                            </span>
                                                            <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                            <p style="margin-top:20px;" ><b> Image Size: {{ $vendor_estore_size }} </b></p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            @if($Vendor->estore_image)
                                            <div class="col-sm-12"  id="uploaded_image_estore">

                                                <label for="estore_image" class="col-sm-2 control-label">Uploaded {{ $module_name }} Image</label>

                                                <div class="col-sm-10">
                                                    <img src="{{ url('public/vendors_images/'.$Vendor->estore_image) }}" alt="Uploaded Profile Image" style="{{ $vendor_estore_WH }}">

                                                    <div class="col-sm-12" style="margin-top:20px;">
                                                        <a href="javascript:void(0);" class="btn btn-orange fileinput-exists" data-dismiss="fileinput" id="remove_image_estore">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                        </div>

                                    </div>
                                    @endif
                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="panel panel-primary" data-collapsed="0">

                                <!-- panel head -->
                                <div class="panel-heading">
                                    <div class="panel-title">Contract Details</div>

                                </div>

                                <!-- panel body -->
                                <div class="panel-body">
                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('acc_name') ? ' has-error' : '' }}">
                                            <label for="acc_name" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Contract Name</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="acc_name" autocomplete="off" value="{{ $Vendor->contract_name }}" name="acc_name"  readonly="readonly"  disabled="disabled">
                                                @if ($errors->has('acc_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('acc_name') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('contract_startdate') ? ' has-error' : '' }}">
                                            <label for="contract_startdate" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Start Date</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="contract_startdate" autocomplete="off" value="{{ $Vendor->contract_startdate }}" name="contract_startdate"  readonly="readonly"  disabled="disabled">
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
                                                <input type="text" class="form-control" id="contract_enddate" autocomplete="off" value="{{ $Vendor->contract_enddate }}" name="contract_enddate"  readonly="readonly" disabled="disabled">
                                                @if ($errors->has('contract_enddate'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('contract_enddate') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4 hide">

                            <div class="panel panel-primary" data-collapsed="0">



                                <!-- panel body -->
                                <div class="panel-body ">
                                    <div class="row margin-btm10">
                                        <div class="col-sm-12{{ $errors->has('sale_setting') ? ' has-error' : '' }}">
                                            <label for="acc_name" class="col-sm-12 control-label" style="text-align:left;margin-bottom:10px;">Sales Count Date</label>

                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="sale_setting" autocomplete="off" value="{{ $Vendor->sale_setting }}" name="sale_setting">
                                                @if ($errors->has('sale_setting'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('sale_setting') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>


                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">

                            <div class="panel panel-primary" data-collapsed="0">

                                <!-- panel head -->
                                <div class="panel-heading">
                                    <div class="panel-title">General Information</div>

                                </div>

                                <div class="panel-body">

                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="col-sm-6{{ $errors->has('name_ar') ? ' has-error' : '' }}">
                                                <label for="name_ar" class="col-sm-4 control-label">Name(AR)</label>

                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ $Vendor->name_ar }}" name="name_ar">
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
                                            <div class="col-sm-12{{ $errors->has('description_en') ? ' has-error' : '' }}">
                                                <label for="description_en" class="col-sm-2 control-label">Description(EN)</label>
                                                <div class="col-sm-10">
                                                    <textarea  class="form-control resize" name="description_en" id="description_en" >{{ $Vendor->description_en }}</textarea>
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
                                                    <textarea  class="form-control resize" name="description_ar" id="description_ar" dir="rtl" >{{ $Vendor->description_ar }}</textarea>
                                                    @if ($errors->has('description_ar'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('description_ar') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    
                                    @if(str_contains($Vendor->modules, '4'))                                       
                                     <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="col-sm-6{{ $errors->has('estore_offer_text_en') ? ' has-error' : '' }}">
                                                <label for="estore_offer_text_en" class="col-sm-4 control-label">{{ $module_name }} Offer(EN)</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="estore_offer_text_en" autocomplete="off" value="{{ $Vendor->estore_offer_text_en }}" name="estore_offer_text_en">
                                                    @if ($errors->has('estore_offer_text_en'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('estore_offer_text_en') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-6{{ $errors->has('estore_offer_text_ar') ? ' has-error' : '' }}">
                                                <label for="estore_offer_text_ar" class="col-sm-4 control-label">{{ $module_name }} Offer(AR)</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="estore_offer_text_ar" autocomplete="off" value="{{ $Vendor->estore_offer_text_ar }}" name="estore_offer_text_ar">
                                                    @if ($errors->has('estore_offer_text_ar'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('estore_offer_text_ar') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     @endif
                                  
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
<script src="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            sale_setting: "required",
            name_ar: "required",
            description_en: "required",
            description_ar: "required",
            profile_image: {
                required: function (element) {
                    if ($('#uploaded_image_removed').val() == 1) {
                        return true;
                    } else {
                        return false;
                    }
                },
            },
            @if(str_contains($Vendor->modules, '4')) 
            estore_image: {
                required: function (element) {
                    if ($('#uploaded_image_removed_estore').val() == 1) {
                        return true;
                    } else {
                        return false;
                    }
                },
            }
        @endif
        },
        errorPlacement: function (error, element) {
            switch (element.attr("name")) {
                case 'profile_image':
                    error.insertAfter($("#error_file"));
                    break;
                    @if(str_contains($Vendor->modules, '4'))
                case 'estore_image':
                    error.insertAfter($("#error_file_estore"));
                    break;
                    @endif
                default:
                    error.insertAfter(element);
            }
        }


    });

    //Remove Uploaded Image
    $('#remove_image').on('click', function (event) {
        $('profile_image').val('');
        $('#uploaded_image').hide('fast');
        $('#uploaded_image_removed').val('1');
        $('#upload_image').show('fast');
    });

    //Remove Estore Uploaded Image
    $('#remove_image_estore').on('click', function (event) {
        $('profile_image_estore').val('');
        $('#uploaded_image_estore').hide('fast');
        $('#uploaded_image_removed_estore').val('1');
        $('#upload_image_estore').show('fast');
    });
});

</script>
<script>
    $(function () {
        /*-------Da            te-----------*/
        $('#sale_setting').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        });
    });
</script>
@endsection