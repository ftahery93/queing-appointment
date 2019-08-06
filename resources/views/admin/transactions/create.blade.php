@extends('layouts.master')

@section('title')
Transactions
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/transactions') }}">Transactions</a>
</li>
@endsection

@section('pageheading')
Transactions
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('/admin/transactions') }}" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin/transactions') }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('user_type') ? ' has-error' : '' }}">
                                <label for="user_type" class="col-sm-3 control-label">User Type</label>
                                <div class="col-sm-9">
                                    <select name="user_type" class="select2" data-allow-clear="true" id="user_type" >
                                        <option value="1" {{ (collect(old('user_type'))->contains(1)) ? 'selected':'' }}> Vendor</option>
                                        <option value="2" {{ (collect(old('user_type'))->contains(2)) ? 'selected':'' }}> Trainer</option>
                                    </select>
                                    @if ($errors->has('user_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('user_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('amount') ? ' has-error' : '' }}">
                                <label for="amount" class="col-sm-3 control-label">Amount {{ config('global.amountCurrency') }}</label>

                                <div class="col-sm-9">
                                    <input type="amount" class="form-control" id="amount" autocomplete="off" value="{{ old('amount') }}" name="amount"> 
                                    @if ($errors->has('amount'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('amount') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('user_id') ? ' has-error' : '' }}"   id="vendor_id">
                                <label for="user_id" class="col-sm-3 control-label">Vendor / Trainer</label>
                                <div class="col-sm-9">
                                    <select name="user_id[vendor]" class="select2" data-allow-clear="true" >

                                        @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ (collect(old('user_id'))->contains($vendor->id)) ? 'selected':'' }} >{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('user_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('user_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-sm-6{{ $errors->has('user_id') ? ' has-error' : '' }}" style="display: none;" id="trainer_id">
                                <label for="user_id" class="col-sm-3 control-label">Vendor / Trainer</label>
                                <div class="col-sm-9">
                                    <select name="user_id[trainer]" class="select2" data-allow-clear="true" >

                                        @foreach ($trainers as $trainer)
                                        <option value="{{ $trainer->id }}" {{ (collect(old('user_id'))->contains($trainer->id)) ? 'selected':'' }} >{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('user_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('user_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('transferred_date') ? ' has-error' : '' }}">
                                <label for="transferred_date" class="col-sm-3 control-label">Date of Transfer</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="transferred_date" autocomplete="off"  value="{{ old('transferred_date') }}" name="transferred_date">
                                    @if ($errors->has('transferred_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('transferred_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('comment') ? ' has-error' : '' }}">
                                <label for="comment" class="col-sm-3 control-label">Comment</label>
                                <div class="col-sm-9">
                                    <textarea  class="form-control resize" name="comment" id="description_en" >{{ old('comment') }}</textarea>
                                    @if ($errors->has('comment'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('comment') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">

                                <label for="attachment" class="col-sm-3 control-label">Upload Document</label>

                                <div class="col-sm-9">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="input-group">
                                            <div class="form-control uneditable-input" data-trigger="fileinput">
                                                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                <span class="fileinput-filename"></span>
                                            </div>
                                            <span class="input-group-addon btn btn-default btn-file">
                                                <span class="fileinput-new">Select file</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="attachment">
                                            </span>
                                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
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
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            user_type: "required",
            user_id: "required",
            attachment: "required",
            transferred_date: "required",
            amount: {
                required: true,
                currency: true
            }
        },

    });
    
//Change vendor or trainer selection
    $('#user_type').on('change', function (event) {
        var val=$(this).val();
        if(val==2){
            $('#vendor_id').hide('fast');
             $('#trainer_id').show('fast');
        }
        else{
         $('#trainer_id').hide('fast');
          $('#vendor_id').show('fast');
        }
        
    });
});

</script>
<script>
    $(function () {
        /*-------Date-----------*/
        $('#transferred_date').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        });
    });
</script>
@endsection