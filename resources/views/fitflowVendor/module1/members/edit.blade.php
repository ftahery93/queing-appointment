@extends('vendorLayouts.master')

@section('title')
Members
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM1.'/members') }}">Members</a>
</li>
@endsection

@section('pageheading')
Members
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url($configM1.'/members/'. $Members->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="patch">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('vendorLayouts.flash-message')
            @yield('form-error')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url($configM1.'/members') }}" class="margin-top0">
                            <button type="button" class="btn btn-red btn-icon">
                                Cancel
                                <i class="entypo-cancel"></i>
                            </button>
                        </a>

                    </div>
                </div>

                <div class="panel-body">


                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-sm-3 control-label">Name</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" autocomplete="off" value="{{ $Members->name }}" name="name">
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-sm-3 control-label">Email</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" autocomplete="off" value="{{ $Members->email }}" name="email">
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

                            <div class="col-sm-6{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                <label for="mobile" class="col-sm-3 control-label">Mobile</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control number_only" id="mobile" autocomplete="off" value="{{ $Members->mobile }}" name="mobile">
                                    @if ($errors->has('mobile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6{{ $errors->has('dob') ? ' has-error' : '' }}">
                                <label for="dob" class="col-sm-3 control-label">DOB</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="dob" autocomplete="off"  value="{{ $Members->dob }}" name="dob">
                                    @if ($errors->has('dob'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dob') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('gender_id') ? ' has-error' : '' }}">
                                <label for="gender_id" class="col-sm-3 control-label">Gender</label>

                                <div class="col-sm-9">
                                    <select name="gender_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select--</option>
                                        @foreach ($gender_types as $gender)
                                        <option value="{{ $gender->id }}" @if($Members->gender_id == $gender->id) selected  @endif >{{ $gender->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('gender_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6{{ $errors->has('area_id') ? ' has-error' : '' }}">
                                <label for="area_id" class="col-sm-3 control-label">Area</label>

                                <div class="col-sm-9">
                                    <select name="area_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select--</option>
                                        @foreach ($areas as $area)
                                        <option value="{{ $area->id }}" @if($Members->area_id == $area->id) selected  @endif  >{{ $area->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('area_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('area_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                    <!--Current Package Permission-->
                    @if($currentPackagePermission==1)
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('package_id') ? ' has-error' : '' }}">
                                <label for="package_id" class="col-sm-3 control-label">Package</label>

                                <div class="col-sm-9">
                                    <select name="package_id" class="select2" data-allow-clear="true" id="package_id">
                                        <option value="">--Select--</option>
                                        @foreach ($packages as $package)
                                        <option value="{{ $package->id }}" @if($Members->package_id == $package->id) selected  @endif >{{ $package->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('package_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('package_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="cash" class="col-sm-3 control-label">Cash</label>

                                <div class="col-sm-9">  
                                    <input type="text" class="form-control" id="cash" autocomplete="off"  value="{{ $Members->cash }}" name="cash"
                                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 8 || event.charCode == 46">

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('start_date') ? ' has-error' : '' }}">
                                <label for="start_date" class="col-sm-3 control-label">Start Date</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="start_date" autocomplete="off"  value="{{ $Members->start_date }}" name="start_date">
                                    @if ($errors->has('start_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('start_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="knet" class="col-sm-3 control-label">KNET</label>

                                <div class="col-sm-9"> 
                                    <input type="text" class="form-control" id="knet" autocomplete="off"  value="{{ $Members->knet }}" name="knet"
                                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 8 || event.charCode == 46">

                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label for="end_date" class="col-sm-3 control-label">End Date</label>

                                <div class="col-sm-9">
                                    <input type="end_date" class="form-control" id="end_date" autocomplete="off" disabled="disabled" value="{{ $Members->end_date }}">                                    
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="final_total_amt" class="col-sm-3 control-label">Fee {{ config('global.amountCurrency') }}</label>

                                <div class="col-sm-9">
                                    <input type="final_total_amt" class="form-control" id="final_total_amt"  value="{{ $Members->price }}" autocomplete="off" disabled="disabled">                                    
                                </div>
                            </div>

                        </div>

                    </div>
                    @endif 
                    <!--Current Package Permission-->
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
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            name: "required",
            email: "required",
            dob: "required",
            mobile: {
                required: true,
                number: true,
                minlength: 8,
                maxlength: 12
            },
//            gender_id: {
//                required: true,
//                number: true
//            },
            area_id: {
                required: true,
                number: true
            },
            start_date: {
                required: true,
            },
//            package_id: {
//                required: true,
//                number: true
//            },
        },

    });
    
});

</script>
<script>
    $(function () {
        /*-------Date-----------*/
        $('#start_date').datepicker({
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
            $('#end_date').datepicker('setStartDate', startDate);

            //Ajax call
            var valueSelected1 = $('#package_id').val();
            var start_date = $(this).val();
            if (valueSelected1 == '') {
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
                        $('#end_date').val(data.packages.end_date);
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
        $('#end_date').datepicker({
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
            $('#start_date').datepicker('setEndDate', FromEndDate);
        });

        $('#dob').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
           // endDate: "today",
            toolbarPlacement: 'bottom'
        });
    });
</script>
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
        if($(this).val()==''){
            cash_amt=0;
        }
        var cash_amt = parseFloat($(this).val()).toFixed(3);
        var amt = $('#final_total_amt').val();
        var final_total_amt = parseFloat($('#final_total_amt').val()).toFixed(3);
        $('#cash').val(parseFloat(final_total_amt - cash_amt).toFixed(3));
        if (amt == 0) {
            $('#knet').val(parseFloat(0).toFixed(3));
            $('#cash').val(parseFloat(0).toFixed(3));
        }
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