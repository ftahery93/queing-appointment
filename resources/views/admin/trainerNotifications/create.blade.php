@extends('layouts.master')

@section('title')
Trainer Notifications
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/trainerNotifications')  }}">Trainer Notifications</a>
</li>
@endsection

@section('pageheading')
Trainer Notifications
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('/admin/trainerNotifications') }}" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin/trainerNotifications')  }}" class="margin-top0">
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
                            <div class="col-sm-6{{ $errors->has('trainer_id') ? ' has-error' : '' }}">
                                <label for="trainer_id" class="col-sm-3 control-label">Trainers</label>
                                <div class="col-sm-9">
                                    <select name="trainer_id" class="select2" data-allow-clear="true" id="trainer_id" onchange="GetSelectedTextValue()" >
                                        <option value=" ">--Select Trainer</option>
                                        @foreach ($Trainers as $Trainer)
                                        <option value="{{ $Trainer->id }}"> {{ $Trainer->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('trainer_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('trainer_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6{{ $errors->has('notification_date') ? ' has-error' : '' }}">
                                <label for="notification_date" class="col-sm-3 control-label">DateTime</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="notification_date" autocomplete="off"  value="{{ old('notification_date') }}" name="notification_date">
                                    @if ($errors->has('notification_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('notification_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="send_to" autocomplete="off" value="0" name="send_to">
                    </div>


                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('subject') ? ' has-error' : '' }}">
                                <label for="subject" class="col-sm-3 control-label">Subject</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="subject" autocomplete="off" value="{{ old('subject') }}" name="subject">
                                    @if ($errors->has('subject'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('subject') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('subject_ar') ? ' has-error' : '' }}">
                                <label for="subject_ar" class="col-sm-3 control-label">Subject(AR)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="subject_ar" autocomplete="off" value="{{ old('subject_ar') }}" name="subject_ar" dir="rtl"> 
                                    @if ($errors->has('subject_ar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('subject_ar') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('message') ? ' has-error' : '' }}">
                                <label for="message" class="col-sm-3 control-label">Message(EN)</label>
                                <div class="col-sm-9">
                                    <textarea  class="form-control resize" name="message" id="message" >{{ old('message') }}</textarea>
                                    @if ($errors->has('message'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('message') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('message_ar') ? ' has-error' : '' }}">
                                <label for="message_ar" class="col-sm-3 control-label">Message(AR)</label>
                                <div class="col-sm-9">
                                    <textarea  class="form-control resize" name="message_ar" id="message_ar"  dir="rtl" >{{ old('message_ar') }}</textarea>
                                    @if ($errors->has('message_ar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('message_ar') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

<!--                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('link') ? ' has-error' : '' }}">
                                <label for="link" class="col-sm-3 control-label">Link</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="link" autocomplete="off" value="{{ old('link') }}" name="link">
                                    @if ($errors->has('link'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('link') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>-->


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
<script type="text/javascript">
                                        $(document).ready(function () {

                                            $.validator.addMethod("currency", function (value, element) {
                                                return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
                                            }, "Please specify a valid amount");

                                            var validator = $("#form1").validate({
                                                ignore: 'input[type=hidden], .select2-input, .select2-focusser',
                                                rules: {
                                                    subject: "required",
                                                    message: "required",
                                                    notification_date: "required",
                                                    trainer_id: "required",
                                                    send_to: "required"
                                                },

                                            });

                                        });

</script>
<script>
    $(function () {
        /*-------Date-----------*/
        $('#notification_date').datetimepicker({
            format: 'DD/MM/YYYY hh:mm:A',
            toolbarPlacement: 'bottom',
             minDate: new Date(),
        });
    });
</script>
@endsection