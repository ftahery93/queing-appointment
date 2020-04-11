@extends('layouts.master')

@section('title')
Modules
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/modules') }}">Modules</a>
</li>
@endsection

@section('pageheading')
Modules
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/admin/modules/'. $Module->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin/modules') }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('name_en') ? ' has-error' : '' }}">

                                <label for="name_en" class="col-sm-3 control-label">Name(EN)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name_en" autocomplete="off" value="{{ $Module->name_en }}" name="name_en">
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
                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ $Module->name_ar }}" name="name_ar">
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
                            <div class="col-sm-6{{ $errors->has('description_en') ? ' has-error' : '' }}">
                                <label for="description_en" class="col-sm-3 control-label">Description(EN)</label>
                                <div class="col-sm-9">
                                    <textarea  class="form-control resize" name="description_en" id="description_en" >{{ $Module->description_en }}</textarea>
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
                                    <textarea  class="form-control resize" name="description_ar" id="description_ar" >{{ $Module->description_ar }}</textarea>
                                    @if ($errors->has('description_ar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description_ar') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                    <?php /* ?>
                    <div class="row">
                        <div class="form-group col-sm-12">


                            <div class="col-sm-6">
                                <label for="status" class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                        <option value="1" @if($Module->status == 1) selected  @endif > Active</option>
                                        <option value="0" @if($Module->status == 0) selected  @endif > Deactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php */ ?>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6" @if($Module->icon != '' || $Module->icon!=null) style="display:none;" @endif id="upload_image">
                                <label for="icon" class="col-sm-3 control-label">Icon</label>

                                <div class="col-sm-9">
                                    <div class="fileinput fileinput-new" data-provides="fileinput" id="error_file">
                                        <div class="fileinput-new thumbnail" style="{{ $module_icon_WH }};" data-trigger="fileinput">
                                            <img src="{{ asset('assets/images/album-image-1.jpg') }}" alt="...">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="{{ $module_icon_WH }}"></div>
                                        <div>
                                            <span class="btn btn-white btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="icon" accept="image/*" id="profile_image">
                                                <input type="hidden" name="uploaded_image_removed" id="uploaded_image_removed" value="0">
                                            </span>
                                            <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                                            <p style="margin-top:20px;" ><b> Image Size: {{ $module_icon_size }} </b></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @if($Module->icon)
                            <div class="col-sm-6"  id="uploaded_image">

                                <label for="icon" class="col-sm-3 control-label">Uploaded Icon</label>

                                <div class="col-sm-9">
                                    <img src="{{ url('public/modules_icons/'.$Module->icon) }}" alt="Uploaded Icon" style="{{ $module_icon_WH }}">

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
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            name_en: "required",
            name_ar: "required",
            description_en: "required",
            description_ar: "required",
             icon: {
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
        errorPlacement: function (error, element) {
            switch (element.attr("name")) {
                case 'icon':
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

<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        $('input.icheck-14').iCheck({
            checkboxClass: 'icheckbox_polaris',
            radioClass: 'iradio_polaris'
        });

    });
</script>

@endsection