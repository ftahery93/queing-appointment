@extends('trainerLayouts.master')

@section('title')
Branches
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

    <a href="{{ url('trainer/branches') }}">Branches</a>
</li>
@endsection

@section('pageheading')
Branches
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/trainer/branches/'. $TrainerPackage->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="patch">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('trainerLayouts.flash-message')
            @yield('form-error')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    {{-- <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url('trainer/branches') }}" class="margin-top0">
                            <button type="button" class="btn btn-red btn-icon">
                                Cancel
                                <i class="entypo-cancel"></i>
                            </button>
                        </a>

                    </div> --}}
                </div>

                <div class="panel-body">


                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('name_en') ? ' has-error' : '' }}">
                                <label for="name_en" class="col-sm-3 control-label">Name(EN)</label>

                                <div class="col-sm-9">
                                    {{ $TrainerPackage->name_en }}
                                </div>

                            </div>
                            
                        </div>

                    </div>

              
                    <div class="row">
                        <div class="form-group col-sm-12">

                            

                            <div class="col-sm-6{{ $errors->has('working_hours') ? ' has-error' : '' }}">
                                <label for="working_hours" class="col-sm-3 control-label">Working Hours</label>

                                <div class="col-sm-9">
                                    {{ $TrainerPackage->working_hours }}
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('address') ? ' has-error' : '' }}">
                                <label for="address" class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-9">
                                    {{ $TrainerPackage->address }}
                                </div>
                            </div>
                            

                        </div>

                    </div>
                    

                    <div class="row">
                      <div class="form-group col-sm-12">

                      <div class="col-sm-6{{ $errors->has('latitude') ? ' has-error' : '' }}">

                      <label for="latitude" class="col-sm-3 control-label">Latitude</label>

                      <div class="col-sm-9">
                        {{ $TrainerPackage->latitude }}
                      </div>

                      </div>

                      <div class="col-sm-6{{ $errors->has('longitude') ? ' has-error' : '' }}">
                      <label for="longitude" class="col-sm-3 control-label">Longitude</label>

                      <div class="col-sm-9">
                        {{ $TrainerPackage->longitude }}
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
                latitude: "required",
                longitude: "required",
                address: "required",
            },

        });

    });

</script>
@endsection