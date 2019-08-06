@extends('layouts.master')

@section('title')
Faq
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

    <a href="{{ url('admin/faq') }}">Faq</a>
</li>
@endsection

@section('pageheading')
Faq
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('/admin/faq') }}" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin/faq') }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('question_en') ? ' has-error' : '' }}">
                                <label for="question_en" class="col-sm-3 control-label">Question(EN)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="question_en" autocomplete="off" value="{{ old('question_en') }}" name="question_en">
                                    @if ($errors->has('question_en'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('question_en') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('question_ar') ? ' has-error' : '' }}">
                                <label for="question_ar" class="col-sm-3 control-label">Question(AR)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="question_ar" autocomplete="off" value="{{ old('question_ar') }}" name="question_ar" dir="rtl">
                                    @if ($errors->has('question_ar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('question_ar') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>
                    
                    
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('answer_en') ? ' has-error' : '' }}">
                                <label for="answer_en" class="col-sm-3 control-label">Answer(EN)</label>
                                <div class="col-sm-9">
                                    <textarea  class="form-control resize" name="answer_en" id="answer_en" >{{ old('answer_en') }}</textarea>
                                    @if ($errors->has('answer_en'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer_en') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                             <div class="col-sm-6{{ $errors->has('answer_ar') ? ' has-error' : '' }}">
                                <label for="answer_ar" class="col-sm-3 control-label">Answer(AR)</label>
                                <div class="col-sm-9">
                                    <textarea  class="form-control resize" name="answer_ar" id="answer_ar"  dir="rtl">{{ old('answer_ar') }}</textarea>
                                    @if ($errors->has('answer_ar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer_ar') }}</strong>
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
                question_en: "required",
                question_ar: "required",
                answer_en: "required",
                answer_ar: "required"
            },

        });

    });

</script>
@endsection