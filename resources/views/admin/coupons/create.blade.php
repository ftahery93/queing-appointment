@extends('layouts.master')

@section('title')
Coupons
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/coupons')  }}">Coupons</a>
</li>
@endsection

@section('pageheading')
Coupons
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('/admin/coupons') }}" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin/coupons')  }}" class="margin-top0">
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
                            
                            <div class="col-sm-6{{ $errors->has('vendor_id') ? ' has-error' : '' }}">
                                <label for="vendor_id" class="col-sm-3 control-label">Vendors</label>
                                <div class="col-sm-9">
                                    <select name="vendor_id" class="select2" data-allow-clear="true">
                                         <option value="">--Select--</option>
                                        <option value="0">--All Vendors--</option>
                                        @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ (collect(old('vendor_id'))->contains($vendor->id)) ? 'selected':'' }} >{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('vendor_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('vendor_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div> 
                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('name_en') ? ' has-error' : '' }}">
                                <label for="name_en" class="col-sm-3 control-label">Coupon Name(EN)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name_en" autocomplete="off" value="{{ old('name_en') }}" name="name_en">
                                    @if ($errors->has('name_en'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name_en') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('name_ar') ? ' has-error' : '' }}">
                                <label for="name_ar" class="col-sm-3 control-label">Coupon Name(AR)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ old('name_ar') }}" name="name_ar" dir="rtl">
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

                            <div class="col-sm-6{{ $errors->has('type') ? ' has-error' : '' }}">
                                <label for="type" class="col-sm-3 control-label">Type</label>
                                <div class="col-sm-9">
                                    <select name="type" class="select2" data-allow-clear="true" >           
                                        <option value="">--Type--</option>
                                        <option value="1" {{ (collect(old('type'))->contains(1)) ? 'selected':'' }} >Percentage</option>
                                        <option value="2" {{ (collect(old('type'))->contains(2)) ? 'selected':'' }} >Fixed Amount</option>                          
                                    </select>
                                    @if ($errors->has('type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('discount') ? ' has-error' : '' }}">
                                <label for="discount" class="col-sm-3 control-label">Discount</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control" id="discount" autocomplete="off"  name="discount" value="{{ old('discount') }}"> 
                                    @if ($errors->has('discount'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('discount') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('code') ? ' has-error' : '' }}">
                                <label for="code" class="col-sm-3 control-label">Code</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="code" autocomplete="off" value="{{ old('code') }}" name="code">
                                    @if ($errors->has('code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('code') }}</strong>
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

                            <div class="col-sm-6{{ $errors->has('start_date') ? ' has-error' : '' }}">
                                <label for="start_date" class="col-sm-3 control-label">Start Date</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="start_date" autocomplete="off"  value="{{ old('start_date') }}" name="start_date">
                                    @if ($errors->has('start_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('start_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('end_date') ? ' has-error' : '' }}">
                                <label for="end_date" class="col-sm-3 control-label">End Date</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="end_date" autocomplete="off"  value="{{ old('end_date') }}" name="end_date">
                                    @if ($errors->has('end_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('end_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('uses_total') ? ' has-error' : '' }}">
                                <label for="uses_total" class="col-sm-3 control-label">Uses Per Coupon</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control number_only" id="uses_total" autocomplete="off" value="{{ old('uses_total') }}" name="uses_total">
                                    @if ($errors->has('uses_total'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('uses_total') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('uses_customer') ? ' has-error' : '' }}">
                                <label for="uses_customer" class="col-sm-3 control-label">Uses Per Customer</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control number_only" id="uses_customer" autocomplete="off" value="{{ old('uses_customer') }}" name="uses_customer">
                                    @if ($errors->has('uses_customer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('uses_customer') }}</strong>
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

</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");
    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            vendor_id: "required",
            name_en: "required",
            name_ar: "required",
            type: "required",
            discount: {
                required: true,
                currency: true
            },
            code: "required",
            uses_total: {
                required: true,
                number: true
            },
            uses_customer: {
                required: true,
                number: true
            },
            start_date: "required",
            end_date: "required"
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
    });
</script>
@endsection