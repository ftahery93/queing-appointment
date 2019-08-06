@extends('layouts.master')

@section('title')
Sponsored Ads
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/sponsoredAds') }}">Sponsored Ads</a>
</li>
@endsection

@section('pageheading')
Sponsored Ads
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/admin/sponsoredAds/'. $sponsoredAds->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="patch">

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
                        <a href="{{ url('admin/sponsoredAds') }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('start_date') ? ' has-error' : '' }}">

                                <label for="start_date" class="col-sm-3 control-label">Start Date</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="start_date" autocomplete="off" value="{{ $sponsoredAds->start_date }}" name="start_date">
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
                                    <input type="text" class="form-control datetimepicker" id="end_date" autocomplete="off" value="{{ $sponsoredAds->end_date }}" name="end_date">
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


                            <div class="col-sm-6">
                                <label for="status" class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                        <option value="1" @if($sponsoredAds->status == 1) selected  @endif > Active</option>
                                        <option value="0" @if($sponsoredAds->status == 0) selected  @endif > Deactive</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6" @if($sponsoredAds->image != '' || $sponsoredAds->image!=null) style="display:none;" @endif id="upload_image">

                                <label for="image" class="col-sm-3 control-label">Image</label>

                                <div class="col-sm-9">
                                    <div class="fileinput fileinput-new" data-provides="fileinput" id="error_file">
                                        <div class="fileinput-new thumbnail" style="{{ $sponsoredAd_image_WH }};" data-trigger="fileinput">
                                            <img src="{{ asset('assets/images/album-image-1.jpg') }}" alt="...">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="{{ $sponsoredAd_image_WH }}"></div>
                                        <div>
                                            <span class="btn btn-white btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="image" accept="image/*" id="profile_image">
                                                <input type="hidden" name="uploaded_image_removed" id="uploaded_image_removed" value="0">
                                            </span>
                                            <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                                            <p style="margin-top:20px;" ><b> Image Size: {{ $sponsoredAd_image_size }} </b></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @if($sponsoredAds->image)
                            <div class="col-sm-6"  id="uploaded_image">

                                <label for="image" class="col-sm-3 control-label">Uploaded Image</label>

                                <div class="col-sm-9">
                                    <img src="{{ url('public/sponsoredAd_images/'.$sponsoredAds->image) }}" alt="Uploaded Icon" style="width:250px;height:250px;">

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

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            start_date: "required",
            end_date: "required",             
            image: {
                    required: function(element) {
                   if ($('#uploaded_image_removed').val()==1) {
                   return true;
                   }
                   else {
                   return false;
                   }
                 },                  
               }
             
        },
        errorPlacement: function(error, element) {
        switch (element.attr("name")) {
        case 'image':
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