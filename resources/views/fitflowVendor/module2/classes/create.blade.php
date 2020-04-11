@extends('vendorLayouts.master')

@section('title')
Branch wise Class - {{  $className }}
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
<style>
    .ev_src_Gr{height:250px;}
</style>
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM2.'/'.$classMasterID.'/classes') }}"> {{  $className }}</a>
</li>
@endsection

@section('pageheading')
Branch wise Class - {{  $className }}
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url($configM2.'/'.$classMasterID.'/classes') }}" id="form1" enctype="multipart/form-data" name="form1">
    {{ method_field('POST') }}
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
                        <a href="{{ url($configM2.'/'.$classMasterID.'/classes') }}" class="margin-top0">
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
                            <input type="hidden" name="vendor_id" class="form-control" autocomplete="off" value="{{ VendorDetail::getID() }}">
                            <input type="hidden" name="class_master_id" class="form-control" autocomplete="off" value="{{ $classMasterID }}">
                            <div class="col-sm-6{{ $errors->has('gender_type') ? ' has-error' : '' }}">
                                <label for="gender_type" class="col-sm-3 control-label">Member Type</label>
                                <div class="col-sm-9">
                                    <select name="gender_type" class="select2" data-allow-clear="true"   id="gender">

                                        @foreach ($gender_types as $gender_type)
                                        <option value="{{ $gender_type->id }}" {{ (collect(old('gender_type'))->contains($gender_type->id)) ? 'selected':'' }} >{{ $gender_type->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('gender_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('branch_id') ? ' has-error' : '' }}">
                                <label for="branch_id" class="col-sm-3 control-label">Branches</label>
                                <div class="col-sm-9">
                                    <select name="branch_id" class="select2" data-allow-clear="true">
                                        <option value="">--Select Branches--</option>
                                        @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ (collect(old('branch_id'))->contains($branch->id)) ? 'selected':'' }} >{{ $branch->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('branch_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('branch_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                        </div>

                    </div> 


                    <div class="row">
                        <div class="form-group col-sm-12">

                            <!--                            <div class="col-sm-6{{ $errors->has('price') ? ' has-error' : '' }}">
                                                            <label for="price" class="col-sm-3 control-label">Price ({{ config('global.amountCurrency') }})</label>
                            
                                                            <div class="col-sm-9">
                                                                <input type="tel" class="form-control" id="price" autocomplete="off"  name="price" value="{{ old('price') }}"> 
                                                                @if ($errors->has('price'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('price') }}</strong>
                                                                </span>
                                                                @endif
                                                            </div>
                            
                                                        </div>-->
                            <div class="col-sm-6{{ $errors->has('trainer_name_en') ? ' has-error' : '' }}">

                                <label for="trainer_name_en" class="col-sm-3 control-label">Trainer Name</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="trainer_name_en" autocomplete="off" value="{{ old('trainer_name_en') }}" name="trainer_name_en">
                                    @if ($errors->has('trainer_name_en'))

                                    <span class="help-block">
                                        <strong>{{ $errors->first('trainer_name_en') }}</strong>
                                    </span>

                                    @endif
                                </div>

                            </div>
                            <div class="col-sm-6{{ $errors->has('num_seats') ? ' has-error' : '' }}">
                                <label for="num_seats" class="col-sm-3 control-label">Total Seats</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control number_only" id="num_seats" autocomplete="off"  value="{{ old('num_seats') }}" name="num_seats">
                                    @if ($errors->has('num_seats'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('num_seats') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('fitflow_seats') ? ' has-error' : '' }}">
                                <label for="fitflow_seats" class="col-sm-3 control-label">{{ $appTitle->title }} Seats</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control number_only" id="fitflow_seats" autocomplete="off"  name="fitflow_seats" value="{{ old('fitflow_seats') }}"> 
                                    @if ($errors->has('fitflow_seats'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fitflow_seats') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="col-sm-6{{ $errors->has('available_seats') ? ' has-error' : '' }}">
                                <label for="available_seats" class="col-sm-3 control-label">Gym Seats</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control number_only" id="available_seats" autocomplete="off"  value="{{ old('num_seats')-old('fitflow_seats') }}" name="available_seats" readonly="readonly" >
                                    @if ($errors->has('available_seats'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('available_seats') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('hours') ? ' has-error' : '' }}">
                                <label for="hours" class="col-sm-3 control-label">Hours (Min)</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control number_only" id="hours" autocomplete="off"  value="{{ old('hours') }}" name="hours">
                                    @if ($errors->has('hours'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('hours') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="status" class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
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
                            <div class="col-sm-6{{ $errors->has('description_en') ? ' has-error' : '' }}">
                                <label for="description_en" class="col-sm-3 control-label">Description(EN)</label>
                                <div class="col-sm-9">
                                    <textarea  class="form-control resize" name="description_en" id="description_en" >{{ old('description_en') }}</textarea>
                                    @if ($errors->has('description_en'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description_en') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('description_ar') ? ' has-error' : '' }}">
                                <label for="description_ar" class="col-sm-3 control-label">Description(AR)</label>
                                <div class="col-sm-9">
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
<script type="text/javascript">
$(document).ready(function () {

    //Add value 0 for unlimited num_points
    $('#minimal-checkbox-1-14').on('ifChecked', function (event) {
        $('.datetimepicker').val('').attr('readonly', 'readonly');
        $('#fullshift').val(1);
    });
    $('#minimal-checkbox-1-14').on('ifUnchecked', function (event) {
        $('.datetimepicker').val('').removeAttr('readonly');
        $('#fullshift').val(0);
    });
    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");
    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            class_id: "required",
            vendor_id: "required",
            gender_type: "required",
            branch_id: "required",
            price: "required",
            num_seats: "required",
            fitflow_seats: "required",
            hours: {
                required: true,
                number: true
            }
        },
    });
});</script>

<script>
    //on change cash
    $(document).on('change', '#fitflow_seats', function () {
        var fseats = parseFloat($(this).val());
        var seats = $('#num_seats').val();
        if (fseats > seats) {
            $('#available_seats').val(0);
            $('#fitflow_seats').val(0);
        } else {
            $('#available_seats').val(parseFloat(seats - fseats));
            if (seats == 0) {
                $('#available_seats').val(0);
                $('#fitflow_seats').val(0);
            }
        }
    });
    $('.number_only').keypress(function (e) {
        return isNumbers(e, this);
    });
    function isNumbers(evt, element)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (
                (charCode != 46 || $(element).val().indexOf('.') != -1) && // “.�? CHECK DOT, AND ONLY ONE.
                (charCode > 57))
            return false;
        return true;
    }

</script>
@endsection