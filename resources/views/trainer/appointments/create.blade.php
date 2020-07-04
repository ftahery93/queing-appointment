@extends('trainerLayouts.master')

@section('title')
appointments
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('trainer/appointments') }}">appointments</a>
</li>
@endsection

@section('pageheading')
appointments
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('/trainer/appointments') }}" id="form1" enctype="multipart/form-data">
    {{ method_field('POST') }}
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('trainerLayouts.flash-message')
            @yield('form-error')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url('trainer/appointments') }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('branch_id') ? ' has-error' : '' }}">
                                <label for="branch_id" class="col-sm-3 control-label">Branch</label>
                                <div class="col-sm-9">
                                    <select name="branch_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select Branch</option>
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

                            <div class="col-sm-6{{ $errors->has('service_id') ? ' has-error' : '' }}">
                                <label for="service_id" class="col-sm-3 control-label">Service</label>
                                <div class="col-sm-9">
                                    <select name="service_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select Branch</option>
                                        @foreach ($services as $branch)
                                        <option value="{{ $branch->id }}" {{ (collect(old('service_id'))->contains($branch->id)) ? 'selected':'' }} >{{ $branch->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('service_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('service_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    

                    <div class="row">
                        <div class="form-group col-sm-12">

                           
                            <div class="col-sm-6{{ $errors->has('slot1') ? ' has-error' : '' }}">
                                <label for="slot1" class="col-sm-3 control-label">First Booking Slot</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="slot1" autocomplete="off"  value="" name="slot1">
                                    @if ($errors->has('slot1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('slot1') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('slot2') ? ' has-error' : '' }}">
                                <label for="slot2" class="col-sm-3 control-label">Last Booking Slot</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="slot2" autocomplete="off"  value="" name="slot2">
                                    @if ($errors->has('slot2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('slot2') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('time_interval') ? ' has-error' : '' }}">
                                <label for="time_interval" class="col-sm-3 control-label">Time Interval (min)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="time_interval" autocomplete="off"  value="" name="time_interval">
                                    @if ($errors->has('time_interval'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('time_interval') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-sm-6{{ $errors->has('num_persons') ? ' has-error' : '' }}">
                                <label for="num_persons" class="col-sm-3 control-label">Persons per booking</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="num_persons" autocomplete="off"  value="" name="num_persons">
                                    @if ($errors->has('num_persons'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('num_persons') }}</strong>
                                    </span>
                                    @endif
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
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<script type="text/javascript">
jQuery(document).ready(function ($)
{
    $('input.icheck-14').iCheck({
        checkboxClass: 'icheckbox_polaris',
        radioClass: 'iradio_polaris'
    });

     //Add value 0 for unlimited num_points
    $('#minimal-checkbox-1-14').on('ifChecked', function (event) {
        $('#num_points').val('0').attr('type','hidden');
        $('#num_class label.error').remove();
    });
    $('#minimal-checkbox-1-14').on('ifUnchecked', function (event) {
        $('#num_points').val('').attr('type','tel');
    });
    
     //Add value 0 for not any offer
    $('#offer').on('ifChecked', function (event) {
        $('#has_offer').val('1');
    });
    $('#offer').on('ifUnchecked', function (event) {
        $('#has_offer').val('0');
    });

});
</script>

<script type="text/javascript">
    $(document).ready(function () {

        $.validator.addMethod("currency", function (value, element) {
            return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
        }, "Please specify a valid amount");

        var validator = $("#form1").validate({
            ignore: 'input[type=hidden], .select2-input, .select2-focusser',
            rules: {
                name_en: "required",
                branch_id: "required",
                service_id: "required",
                slot1: "required",
                slot2: "required",
                time_interval: "required",
                num_person: "required",
            },

        });

    });

</script>
@endsection