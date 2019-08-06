@extends('layouts.master')

@section('title')
{{ ucfirst($trainerName->name) }} - Transactions
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin').'/'.$trainer_id.'/trainertransactions' }}">Transactions</a>
</li>
@endsection

@section('pageheading')
{{ ucfirst($trainerName->name) }} - Transactions
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/admin/trainertransactions/'. $Transaction->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin').'/'.$trainer_id.'/trainertransactions' }}" class="margin-top0">
                            <button type="button" class="btn btn-red btn-icon">
                                Cancel
                                <i class="entypo-cancel"></i>
                            </button>
                        </a>

                    </div>
                </div>

                <div class="panel-body">
                    <input type="hidden" name="user_type" value="2"  id="user_type">

                    <div class="row">
                        <div class="form-group col-sm-12">

                             <div class="col-sm-6{{ $errors->has('reference_num') ? ' has-error' : '' }}">
                                <label for="reference_num" class="col-sm-3 control-label">Reference No.</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control" id="reference_num" autocomplete="off" value="{{ $Transaction->reference_num }}" name="reference_num"> 
                                    @if ($errors->has('reference_num'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('reference_num') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('amount') ? ' has-error' : '' }}">
                                <label for="amount" class="col-sm-3 control-label">Amount {{ config('global.amountCurrency') }}</label>

                                <div class="col-sm-9">
                                    <input type="amount" class="form-control" id="amount number_only" autocomplete="off" value="{{ $Transaction->amount }}" name="amount"> 
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

                            <div class="col-sm-6{{ $errors->has('payment_mode') ? ' has-error' : '' }}">
                                <label for="payment_mode" class="col-sm-3 control-label">Payment Mode</label>
                                <div class="col-sm-9">
                                    <select name="payment_mode" class="select2" data-allow-clear="true" >
                                        <option value="">--Select Payment Mode--</option>
                                        @foreach ($paymentModes as $paymentMode)
                                        <option value="{{ $paymentMode->id }}" @if ($Transaction->payment_mode==$paymentMode->id) selected  @endif >{{ $paymentMode->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('payment_mode'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('payment_mode') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                           
                            <div class="col-sm-6{{ $errors->has('transferred_date') ? ' has-error' : '' }}">
                                <label for="transferred_date" class="col-sm-3 control-label">Date</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="transferred_date" autocomplete="off"  value="{{ $Transaction->transferred_date }}" name="transferred_date">
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
                                    <textarea  class="form-control resize" name="comment" id="description_en" >{{ $Transaction->comment }}</textarea>
                                    @if ($errors->has('comment'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('comment') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">

                                <label for="attachment" class="col-sm-3 control-label">Upload Slip</label>

                                <div class="col-sm-9">
                                    <div class="fileinput fileinput-new" data-provides="fileinput" id="error_file">
                                        <div class="input-group">
                                            <div class="form-control uneditable-input" data-trigger="fileinput">
                                                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                <span class="fileinput-filename">{{ $Transaction->attachment }}</span>
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
<script src="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.js') }}"></script>
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
            reference_num: {
                required: true,
                number: true
            },
            payment_mode: "required",
            user_type: "required",
            transferred_date: "required",
            amount: {
                required: true,
                currency: true
            }
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
                                    (charCode != 46 || $(element).val().indexOf('.') != - 1) && // “.�? CHECK DOT, AND ONLY ONE.
                                    (charCode > 57))
                                    return false;
                            return true;
                            }
</script>
<script>
    $(function () {
        /*-------Da            te-----------*/
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