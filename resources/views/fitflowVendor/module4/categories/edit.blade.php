@extends('vendorLayouts.master')

@section('title')
Categories
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

    <a href="{{ url($configM4.'/categories') }}">Categories</a>
</li>
@endsection

@section('pageheading')
Categories
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url($configM4.'/categories/'. $Category->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url($configM4.'/categories') }}" class="margin-top0">
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
                            <div class="col-sm-6">
                                <label for="parent_id" class="col-sm-3 control-label">Category</label>

                                <div class="col-sm-9">
                                    <select name="parent_id" class="select2" data-allow-clear="true" id="parent_id" >
                                        <option value="0" {{ (collect(old('parent_id'))->contains(0)) ? 'selected':0 }}>-- Main Category --</option>

                                        @foreach($allSubCategories as $subCate)
                                        @if ($subCate->id!=$Category->id)
                                        <option value="{{ $subCate->id }}" @if ($subCate->id==$Category->parent_id)  selected  @endif> {{ ucfirst($subCate->name_en) }}</option>
                                         @endif                                       
                                        @endforeach()
                                    </select>

                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('name_en') ? ' has-error' : '' }}">

                                <label for="name_en" class="col-sm-3 control-label">Name(EN)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name_en" autocomplete="off" value="{{ $Category->name_en }}" name="name_en">
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
                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ $Category->name_ar }}" name="name_ar">
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


                            <div class="col-sm-6">
                                <label for="status" class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                        <option value="1" @if($Category->status == 1) selected  @endif > Active</option>
                                        <option value="0" @if($Category->status == 0) selected  @endif > Deactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                                <label for="sort_order" class="col-sm-3 control-label">Sort Order</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control number_only" id="sort_order" autocomplete="off" value="{{ $Category->sort_order }}" name="sort_order"> 
                                    @if ($errors->has('sort_order'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sort_order') }}</strong>
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
<script type="text/javascript">
$(document).ready(function () {

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            name_en: "required",
            name_ar: "required",
            parent_id: "required",
            sort_order: {
                required: true,
                number: true
            },
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
@endsection