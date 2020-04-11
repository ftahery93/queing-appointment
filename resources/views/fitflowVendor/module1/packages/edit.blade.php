@extends('vendorLayouts.master')

@section('title')
Packages
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

    <a href="{{ url($configM1.'/packages') }}">Packages</a>
</li>
@endsection

@section('pageheading')
Packages
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url($configM1.'/packages/'. $VendorPackage->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url($configM1.'/packages') }}" class="margin-top0">
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
                                <label for="branch_id" class="col-sm-3 control-label">Branches</label>
                                <div class="col-sm-9">
                                    <select name="branch_id" class="select2" data-allow-clear="true">
                                        <option value="">--Select Branches--</option>
                                        @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @if ($VendorPackage->branch_id==$branch->id) selected  @endif >{{ $branch->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('branch_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('branch_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">
                                   <label for="offer" class="col-sm-3 control-label">Any Offer</label>
                                   <div class="col-sm-9">
                                    <input tabindex="5" type="checkbox" class="icheck-14 col-sm-4" id="offer" 
                                     @if($VendorPackage->has_offer == 1) checked  @endif>
                                     <input type="hidden" name="has_offer" id="has_offer"  value="{{ $VendorPackage->has_offer }}" >
                                </div>
                                </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <input type="hidden" name="vendor_id" class="form-control" autocomplete="off" value="{{ VendorDetail::getID() }}">
                            <div class="col-sm-6{{ $errors->has('name_en') ? ' has-error' : '' }}">
                                <label for="name_en" class="col-sm-3 control-label">Name(EN)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name_en" autocomplete="off" value="{{ $VendorPackage->name_en }}" name="name_en">
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
                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ $VendorPackage->name_ar }}" name="name_ar">
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

                            <div class="col-sm-6{{ $errors->has('price') ? ' has-error' : '' }}">
                                <label for="price" class="col-sm-3 control-label">Price</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control" id="price" autocomplete="off"  name="price" value="{{ $VendorPackage->price }}"> 
                                    @if ($errors->has('price'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                              <div class="col-sm-6{{ $errors->has('num_days') ? ' has-error' : '' }}">
                                <label for="num_days" class="col-sm-3 control-label">No. of Days</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control" id="num_days" autocomplete="off"  value="{{ $VendorPackage->num_days }}" name="num_days">
                                    @if ($errors->has('num_days'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('num_days') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            
                             <div class="col-sm-6{{ $errors->has('expired_notify_duration') ? ' has-error' : '' }}">
                                <label for="expired_notify_duration" class="col-sm-3 control-label">Notify(Days) Before Expired</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control" id="expired_notify_duration" autocomplete="off"  value="{{ $VendorPackage->expired_notify_duration }}" name="expired_notify_duration">
                                    @if ($errors->has('expired_notify_duration'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('expired_notify_duration') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="status" class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                        <option value="1" @if($VendorPackage->status == 1) selected  @endif> Active</option>
                                        <option value="0" @if($VendorPackage->status == 0) selected  @endif> Deactive</option>
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
                                    <textarea  class="form-control resize" name="description_en" id="description_en" >{{ $VendorPackage->description_en }}</textarea>
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
                                    <textarea  class="form-control resize" name="description_ar" id="description_ar" >{{ $VendorPackage->description_ar }}</textarea>
                                    @if ($errors->has('description_ar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description_ar') }}</strong>
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
                name_ar: "required",
                branch_id: "required",
                num_points: {
                    required: true,
                    number: true
                },
                num_days: {
                    required: true,
                    number: true
                },
                expired_notify_duration: {
                    required: true,
                    number: true
                },
                price: {
                    required: true,
                    currency: true
                }
            },

        });

    });

</script>
@endsection