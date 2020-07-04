@extends('layouts.master')

@section('title')
Ministry Users
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/ministryUsers') }}">Ministry Users</a>
</li>
@endsection

@section('pageheading')
Ministry Users
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('/admin/ministryUsers') }}" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin/ministryUsers') }}" class="margin-top0">
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
                                                    <input type="text" class="form-control" id="name" autocomplete="off" value="{{ old('name') }}" name="name">
                                                    @if ($errors->has('name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('name') }}</strong>
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

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");
    
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[a-z0-9\\-]+$/i.test(value);
}, "{{ config('global.alphaNumericValidation') }}");

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
             username: {
                noSpace: true
            },
            name: "required",
           // name_ar: "required",
            // description_en: "required",
            // description_ar: "required",
            email: "required",
            // acc_name: "required",
            // bank_id: "required",
            // contract_startdate: "required",
            // contract_enddate: "required",
            // acc_num: {
            //     required: true,
            //     number: true
            // },
            // ibn_num: {
            //     required: true,
            //     alphanumeric: true
            // },
            // civilid: {
            //     required: true,
            //     number: true,
            //     minlength: 12,
            //     maxlength: 12
            // },
            mobile: {
                required: true,
                number: true,
               // minlength: 8,
                maxlength: 8
            },
            password: {
                required: true,
                minlength: 6
            },
            password_confirmation: {
                required: true,
                minlength: 6,
                equalTo: "#password"
            },
            // activities: "required",
            // commission: {
            //     required: true,
            //     currency: true
            // },
            //profile_image: "required",
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
<script>
    $(function () {
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
</script>
@endsection