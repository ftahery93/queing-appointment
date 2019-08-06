@extends('vendorLayouts.master')

@section('title')
Classes
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<style>
    .ev_src_Gr{height:250px;}
</style>
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM2.'/classMaster') }}">Classes</a>
</li>
@endsection

@section('pageheading')
Classes
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url($configM2.'/classMaster') }}" id="form1" enctype="multipart/form-data" name="form1">
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
                        <a href="{{ url($configM2.'/classMaster') }}" class="margin-top0">
                            <button type="button" class="btn btn-red btn-icon">
                                Cancel
                                <i class="entypo-cancel"></i>
                            </button>
                        </a>

                    </div>
                </div>

                <div class="panel-body">
                    <input type="hidden" name="vendor_id" class="form-control" autocomplete="off" value="{{ VendorDetail::getID() }}">

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('name_en') ? ' has-error' : '' }}">

                                <label for="name_en" class="col-sm-3 control-label">Name(EN)</label>

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
                                <label for="name_ar" class="col-sm-3 control-label">Name(AR)</label>

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
                            <div class="col-sm-6{{ $errors->has('activities') ? ' has-error' : '' }}">
                                <label for="activities" class="col-sm-3 control-label">Activities</label>
                                <div class="col-sm-9">
                                    <select name="activities[]" class="select2" data-allow-clear="true" multiple="multiple"  id="activities">

                                        @foreach ($activities as $amenity)
                                        <option value="{{ $amenity->id }}" {{ (collect(old('activities'))->contains($amenity->id)) ? 'selected':'' }} >{{ $amenity->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('activities'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('activities') }}</strong>
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

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");
    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            name_en: "required",
            name_ar: "required",
            vendor_id: "required",
            activities: "required",
        },
    });
});</script>

@endsection