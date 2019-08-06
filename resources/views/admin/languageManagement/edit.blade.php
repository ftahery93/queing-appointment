@extends('layouts.master')

@section('title')
Language Management
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

    <a href="{{ url('admin/languageManagement') }}">Language Management</a>
</li>
@endsection

@section('pageheading')
Language Management
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/admin/languageManagement/'. $LanguageManagement->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin/languageManagement') }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('label_en') ? ' has-error' : '' }}">
                                <label for="label_en" class="col-sm-3 control-label">Name(EN)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="label_en" autocomplete="off" value="{{ $LanguageManagement->label_en }}" name="label_en">
                                    @if ($errors->has('label_en'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('label_en') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('label_ar') ? ' has-error' : '' }}">
                                <label for="label_ar" class="col-sm-3 control-label">Name(AR)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="label_ar" autocomplete="off" value="{{ $LanguageManagement->label_ar }}" name="label_ar" dir="rtl">
                                    @if ($errors->has('label_ar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('label_ar') }}</strong>
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
                                        <option value="1" @if($LanguageManagement->status == 1) selected  @endif> Active</option>
                                        <option value="0" @if($LanguageManagement->status == 0) selected  @endif> Deactive</option>
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
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        $('input.icheck-14').iCheck({
            checkboxClass: 'icheckbox_polaris',
            radioClass: 'iradio_polaris'
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
                label_en: "required",
                label_ar: "required"
            },

    });

});

</script>
@endsection