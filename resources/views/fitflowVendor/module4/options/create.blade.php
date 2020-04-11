@extends('vendorLayouts.master')

@section('title')
Options
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

    <a href="{{ url($configM4.'/options') }}">Options</a>
</li>
@endsection

@section('pageheading')
Options
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url($configM4.'/options') }}" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url($configM4.'/options') }}" class="margin-top0">
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
                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ old('name_ar') }}" name="name_ar"> 
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

                            <div class="col-sm-6{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                                <label for="sort_order" class="col-sm-3 control-label">Sort Order</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control number_only" id="sort_order" autocomplete="off" value="{{ old('sort_order') }}" name="sort_order"> 
                                    @if ($errors->has('sort_order'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sort_order') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="col-md-12">

                        <div class="panel panel-primary" data-collapsed="0">

                            <!-- panel head -->
                            <div class="panel-heading">
                                <div class="panel-title">Option Value</div>

                            </div>

                            <!-- panel body -->
                            <div class="panel-body">

                                <div class="row">                        


                                    <div class="col-sm-12 after-add-more">
                                        <div class="row">
                                            <div class="col-sm-4{{ $errors->has('option_value_name_en.0') ? ' has-error' : '' }}">
                                                <input type="text" name="option_value_name_en[]" class="form-control" autocomplete="off" placeholder="name(EN)" value="{{ old('option_value_name_en.0') }}">
                                                @if ($errors->has('option_value_name_en.0'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('option_value_name_en.0') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4{{ $errors->has('option_value_name_ar.0') ? ' has-error' : '' }}">
                                                <input type="text" name="option_value_name_ar[]" class="form-control" autocomplete="off" placeholder="name(AR)" value="{{ old('option_value_name_ar.0') }}">
                                                @if ($errors->has('option_value_name_ar.0'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('option_value_name_ar.0') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" name="option_value_sort_order[]" class="form-control number_only" autocomplete="off" placeholder="sort order" value="{{ old('option_value_sort_order.0') }}">
                                            </div>
                                            <div class="col-sm-2">
                                                <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- // Copy Fields-->

                                    <div class="copy hide">

                                        <div class="row clone_add"  style="margin-top:10px">
                                            <div class="col-sm-4">
                                                <input type="text" name="option_value_name_en[]" class="form-control" autocomplete="off" placeholder="name(EN)" disabled="disabled">
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="text" name="option_value_name_ar[]" class="form-control" autocomplete="off" placeholder="name(AR)" disabled="disabled">
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" name="option_value_sort_order[]" class="form-control number_only" autocomplete="off" placeholder="sort order" disabled="disabled">
                                            </div>
                                            <div class="col-sm-1">
                                                <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i></button>
                                            </div>
                                        </div>
                                    </div>
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

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            name_en: "required",
            name_ar: "required",
        }
    });

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
<script type="text/javascript">


    $(document).ready(function () {


        $(".add-more").click(function () {

            var html = $(".copy").html();
            $(".after-add-more").append(html);
            $(".after-add-more input").removeAttr('disabled');
        });
        $("body").on("click", ".remove", function () {

            $(this).parents(".clone_add").remove();
        });
    });
</script>
@endsection