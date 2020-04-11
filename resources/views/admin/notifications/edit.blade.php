@extends('layouts.master')

@section('title')
Notifications
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/notifications')  }}">Notifications</a>
</li>
@endsection

@section('pageheading')
Notifications
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/admin/notifications/'. $Notification->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url('admin/notifications')  }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('subject') ? ' has-error' : '' }}">
                                <label for="subject" class="col-sm-3 control-label">Subject</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="subject" autocomplete="off" value="{{ $Notification->subject }}" name="subject">
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
                                    <input type="text" class="form-control" id="subject_ar" autocomplete="off" value="{{ $Notification->subject_ar }}" name="subject_ar" dir="rtl"> 
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
                                    <textarea  class="form-control resize" name="message" id="message" >{{ $Notification->message }}</textarea>
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
                                    <textarea  class="form-control resize" name="message_ar" id="message_ar"  dir="rtl" >{{ $Notification->message_ar }}</textarea>
                                    @if ($errors->has('message_ar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('message_ar') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <!--                            <div class="col-sm-6{{ $errors->has('link') ? ' has-error' : '' }}">
                                                            <label for="link" class="col-sm-3 control-label">Link</label>
                            
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="link" autocomplete="off" value="{{ $Notification->link }}" name="link">
                                                                @if ($errors->has('link'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('link') }}</strong>
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>-->
                            <div class="col-sm-6{{ $errors->has('send_to') ? ' has-error' : '' }}">
                                <label for="send_to" class="col-sm-3 control-label">Sent To</label>
                                <div class="col-sm-9">
                                    <select name="send_to" class="select2" data-allow-clear="true" >           
                                        <option value="">--Select--</option>
                                        <option value="0" @if($Notification->send_to == 0) selected  @endif >All Application Users</option>
                                        <option value="1" @if($Notification->send_to == 1) selected  @endif >Registered Users</option>
                                        <option value="2" @if($Notification->send_to == 2) selected  @endif >Non-Register Users</option>                                 
                                    </select>
                                    @if ($errors->has('send_to'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('send_to') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('notification_date') ? ' has-error' : '' }}">
                                <label for="notification_date" class="col-sm-3 control-label">DateTime</label>

                                <div class="col-sm-9">                                    
                                    <input type="text" class="form-control datetimepicker" id="notification_date" autocomplete="off"  value="{{ $Notification->notification_date }}" name="notification_date">
                                    @if ($errors->has('notification_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('notification_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>


                    <div class="row">
                        <div class="form-group col-sm-12">


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