@extends('trainerLayouts.master')

@section('title')
Profile
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Profile
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/trainer/user/profile')  }}" method="POST" id="form1" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="PUT">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('trainerLayouts.flash-message')
            @yield('form-error')
            @endif

            @if(Session::has('error'))
            @include('trainerLayouts.flash-message')
            @endif

            @if(Session::has('message'))
            @include('trainerLayouts.flash-message')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url('trainer/dashboard') }}" class="margin-top0">
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

                                <div class="panel-body">

                                    <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-6{{ $errors->has('name') ? ' has-error' : '' }}">
                                                <label for="name" class="col-sm-4 control-label">Name</label>

                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="name" autocomplete="off" value="{{ $Trainer->name }}" name="name">
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
                                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ $Trainer->name_ar }}" name="name_ar">
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

                                            <div class="col-sm-6{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                                <label for="mobile" class="col-sm-4 control-label">Mobile</label>

                                                <div class="col-sm-8">
                                                    <input type="tel" class="form-control number_only" id="mobile" autocomplete="off" value="{{ $Trainer->mobile }}" name="mobile">
                                                    @if ($errors->has('mobile'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('mobile') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-sm-6{{ $errors->has('civilid') ? ' has-error' : '' }}">
                                                <label for="civilid" class="col-sm-4 control-label">Civil ID</label>

                                                <div class="col-sm-8">
                                                    <input type="tel" class="form-control" id="civilid" autocomplete="off"  value="{{ $Trainer->civilid }}" name="civilid">
                                                    @if ($errors->has('civilid'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('civilid') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-6{{ $errors->has('email') ? ' has-error' : '' }}">
                                                <label for="email" class="col-sm-4 control-label">Email ID</label>

                                                <div class="col-sm-8">
                                                    <input type="email" class="form-control" id="email" autocomplete="off" value="{{ $Trainer->email }}" name="email"> 
                                                    @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                            </div>

                                            <div class="col-sm-6{{ $errors->has('commission') ? ' has-error' : '' }}">
                                                <label for="commission" class="col-sm-4 control-label">Admin Commission(%)</label>

                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control number_only" id="commission" autocomplete="off"  value="{{ $Trainer->commission }}" name="commission" readonly="readonly">
                                                    @if ($errors->has('commission'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('commission') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">

                                            <div class="col-sm-6{{ $errors->has('username') ? ' has-error' : '' }}">
                                                <label for="username" class="col-sm-4 control-label">Username</label>

                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="username" autocomplete="off"  value="{{ $Trainer->username }}" name="username" readonly="readonly">
                                                    @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="status" class="col-sm-4 control-label">Status</label>

                                                <div class="col-sm-8">
                                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                                        <option value="1" @if($Trainer->status == 1) selected  @endif> Active</option>
                                                        <option value="0" @if($Trainer->status == 0) selected  @endif> Deactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="col-sm-6{{ $errors->has('gender_type') ? ' has-error' : '' }}">
                                                <label for="gender_type" class="col-sm-4 control-label">Gender</label>
                                                <div class="col-sm-8">
                                                    <select name="gender_type" class="select2" data-allow-clear="true"  id="gender">
                                                        <option value="">--Select Gender--</option>
                                                        @foreach ($gender_types as $gender_type)
                                                        <option value="{{ $gender_type->id }}" @if ($Trainer->gender_type==$gender_type->id) selected  @endif >{{ $gender_type->name_en }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('gender_type'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('gender_type') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-sm-6{{ $errors->has('area') ? ' has-error' : '' }}">
                                                <label for="area" class="col-sm-4 control-label">Area</label>
                                                <div class="col-sm-8">
                                                    <select name="area" class="select2" data-allow-clear="true">
                                                        <option value="">--Select Area--</option>
                                                        @foreach ($areas as $area)
                                                        <option value="{{ $area->id }}" @if ($Trainer->area==$area->id) selected  @endif >{{ $area->name_en }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('area'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('area') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="col-sm-6{{ $errors->has('activities') ? ' has-error' : '' }}">
                                                <label for="activities" class="col-sm-4 control-label">Activities</label>
                                                <div class="col-sm-8">
                                                    <select name="activities[]" class="select2" data-allow-clear="true" multiple="multiple" >

                                                        @foreach ($activities as $activity)
                                                        <option value="{{ $activity->id }}" @if ($collection->contains($activity->id)) selected  @endif >{{ $activity->name_en }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('activities'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('activities') }}</strong>
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
                                                    <textarea  class="form-control resize" name="description_en" id="description_en" >{{ $Trainer->description_en }}</textarea>
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
                                                    <textarea  class="form-control resize" name="description_ar" id="description_ar" dir="rtl" >{{ $Trainer->description_ar }}</textarea>
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

                                            <div class="col-sm-12" @if($Trainer->profile_image != '' || $Trainer->profile_image!=null) style="display:none;" @endif id="upload_image">

                                                 <label for="profile_image" class="col-sm-2 control-label">Profile Image</label>

                                                <div class="col-sm-10">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput" id="error_file">
                                                        <div class="fileinput-new thumbnail" style="{{ $trainer_profile_WH }}" data-trigger="fileinput">
                                                            <img src="{{ asset('assets/images/album-image-1.jpg') }}" alt="...">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="{{ $trainer_profile_WH }}"></div>
                                                        <div>
                                                            <span class="btn btn-white btn-file">
                                                                <span class="fileinput-new">Select image</span>
                                                                <span class="fileinput-exists">Change</span>
                                                                <input type="file" name="profile_image" accept="image/*" id="profile_image">
                                                                <input type="hidden" name="uploaded_image_removed" id="uploaded_image_removed" value="0">
                                                            </span>
                                                            <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                            <p style="margin-top:20px;" ><b> Image Size: {{ $trainer_profile_size }} </b></p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            @if($Trainer->profile_image)
                                            <div class="col-sm-12"  id="uploaded_image">

                                                <label for="profile_image" class="col-sm-2 control-label">Uploaded Profile Image</label>

                                                <div class="col-sm-10">
                                                    <img src="{{ url('public/trainers_images/'.$Trainer->profile_image) }}" alt="Uploaded Profile Image" style="{{ $trainer_profile_WH }}">

                                                    <div class="col-sm-12" style="margin-top:20px;">
                                                        <a href="javascript:void(0);" class="btn btn-orange fileinput-exists" data-dismiss="fileinput" id="remove_image">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

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
                                                <select name="bank_id" class="select2" data-allow-clear="true" disabled="disabled"  >

                                                    @foreach ($banks as $bank)
                                                    <option value="{{ $bank->id }}" @if ($bank->id==$Trainer->bank_id)) selected  @endif >{{ $bank->name_en }}</option>
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
                                                <input type="text" class="form-control" id="acc_name" autocomplete="off" value="{{ $Trainer->acc_name }}" name="acc_name" disabled="disabled">
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
                                                <input type="text" class="form-control" id="acc_num" autocomplete="off" value="{{ $Trainer->acc_num }}" name="acc_num" disabled="disabled">
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
                                                <input type="text" class="form-control" id="ibn_num" autocomplete="off" value="{{ $Trainer->ibn_num }}" name="ibn_num" disabled="disabled">
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
                                                <input type="text" class="form-control" id="contract_name" autocomplete="off" value="{{ $Trainer->contract_name }}" name="contract_name" disabled="disabled">
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
                                                <input type="text" class="form-control" id="contract_startdate" autocomplete="off" value="{{ $Trainer->contract_startdate }}" name="contract_startdate" disabled="disabled" >
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
                                                <input type="text" class="form-control" id="contract_enddate" autocomplete="off" value="{{ $Trainer->contract_enddate }}" name="contract_enddate" disabled="disabled">
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
                    </div>


                </div>

            </div>

        </div>
    </div>

</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");
    
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[a-z0-9\\-]+$/i.test(value);
}, "{{ config('global.alphaNumericValidation') }}");

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            //username: "required",
            name: "required",
            name_ar: "required",
            description_en: "required",
            description_ar: "required",
            email: "required",
            civilid: {
                required: true,
                number: true,
                minlength: 12,
                maxlength: 12
            },
            mobile: {
                required: true,
                number: true,
               // minlength: 8,
                maxlength: 8
            },
            activities: "required",
            profile_image: {
                required: function (element) {
                    if ($('#uploaded_image_removed').val() == 1) {
                        return true;
                    } else {
                        return false;
                    }
                },
            }
        },
        errorPlacement: function (error, element) {
            switch (element.attr("name")) {
                case 'profile_image':
                    error.insertAfter($("#error_file"));
                    break;
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
});

</script>
<script>    
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