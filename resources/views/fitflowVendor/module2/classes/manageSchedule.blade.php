@extends('vendorLayouts.master')

@section('title')
{{ $class_name->name_en }} -Manage Schedules
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/fullcalendar/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
<style>

    #top,
    #calendar.fc-unthemed {
        font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    }

    #top {
        background: #eee;
        border-bottom: 1px solid #ddd;
        padding: 0 10px;
        line-height: 40px;
        font-size: 12px;
        color: #000;
    }


    #calendar  {
        max-width: 100%;
        /*        margin: 40px auto;*/
        padding: 0 10px;
    }
    .loading-image-big{position:absolute;top:0;left:0;z-index: 6;height:100%;background:#fff;width:100%;}


</style>
@endsection

@section('content')

@section('breadcrumb')
@if($class_id!=0)
<li>

    <a href="{{ url($configM2.'/'.$class_master_id.'/classes') }}"> {{  $class_name->name_en }}</a>
</li>
@endif
@endsection

@section('pageheading')
Manage Schedules
@endsection

<div class="row">
    <div class="col-md-12">
        @include('vendorLayouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">

            <div class="panel-body" style="position:relative;">
                <button class="btn btn-success pull-right" id="weeklySchedule"><i class="fa fa-plus"></i> Add Schedules</button>
                <br/><br/><br/>
                <div class="loading-image-big" style="display:none;"><img src='{{ asset('assets/images/loading_error.gif') }}' style="height:150px;position:relative;top:45%;left:40%;"></div>

                <div id='calendar'>

                </div>
            </div>

        </div>

    </div>
</div>
<!-- Wekly Schedule Modal -->
<div class="modal fade" id="myWeeklyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"  style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Add Schedules</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form1" >                 
                    <div class="row">
                        <div class="form-group  col-sm-12">

                            <div class="col-sm-12" @if($class_id!=0) style="display:none;" @endif>
                                 <label for="branch_id" class="col-sm-4 control-label"> Branches</label>
                                <div class="col-sm-8">
                                    <select name="branch_id" class="col-sm-12" data-allow-clear="true" id="branch_id" onchange="GetSelectedTextValue(this.value)"
                                            style="padding:6px 10px;">
                                        <option value="0">--All--</option>
                                        @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"> {{ $branch->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 margin10">
                                <label for="weekly_class_id" class="col-sm-4 control-label"> Classes <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <select name="weekly_class_id" class="col-sm-12" data-allow-clear="true" id="weekly_class_id" style="padding:6px 10px;" @if($class_id!=0) disabled @endif>
                                            <option value="">--Select--</option>
                                        @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" data-hr="{{ $class->hours }}" @if($class_id==$class->id) selected @endif>{{ $class->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 margin10">
                                <label for="start_date" class="col-sm-4 control-label">Start Date <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control datetimepicker" id="start_date" autocomplete="off"  value="" name="start_date">
                                </div>
                            </div>
                            <div class="col-sm-12 margin10">
                                <label for="end_date" class="col-sm-4 control-label">End Date <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control datetimepicker" id="end_date" autocomplete="off"  value="" name="end_date">
                                </div>
                            </div>
                            <div class="col-sm-12 margin10">
                                <label for="week_day" class="col-sm-4 control-label">Day <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <select name="week_day" class="col-sm-12" data-allow-clear="true" id="week_day" style="padding:6px 10px;">
                                        <option value="">--Select Day--</option>
                                        <option value="0">Every Day</option> 
                                        <option value="SUNDAY">Every SUNDAY</option>
                                        <option value="MONDAY">Every MONDAY</option>
                                        <option value="TUESDAY">Every TUESDAY</option>
                                        <option value="WEDNESDAY">Every WEDNESDAY</option>
                                        <option value="THURSDAY">Every THURSDAY</option>
                                        <option value="FRIDAY">Every FRIDAY</option>
                                        <option value="SATURDAY">Every SATURDAY</option> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 margin10">
                                <label for="start_time" class="col-sm-4 control-label">Start Time<span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control datetimepicker" id="start_time" autocomplete="off" name="start_time">                  
                                </div>
                            </div>

                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green btn-icon" id="submit_weekly_record">
                    Save
                    <i class="entypo-check"></i>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"  style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Add Schedules</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form1" >                 
                    <div class="row">
                        <div class="form-group  col-sm-12">

                            <div class="col-sm-12" @if($class_id!=0) style="display:none;" @endif>
                                 <label for="branch_id" class="col-sm-3 control-label"> Branches</label>
                                <div class="col-sm-9">
                                    <select name="branch_id" class="col-sm-12" data-allow-clear="true" id="branch_id" onchange="GetSelectedTextValue(this.value)"
                                            style="padding:6px 10px;">
                                        <option value="0">--All--</option>
                                        @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"> {{ $branch->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 margin10">
                                <label for="class_id" class="col-sm-3 control-label"> Classes <span style="color:red;">*</span></label>
                                <div class="col-sm-9">
                                    <select name="class_id" class="col-sm-12" data-allow-clear="true" id="class_id" style="padding:6px 10px;" @if($class_id!=0) disabled @endif>
                                            <option value="">--Select--</option>
                                        @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" data-hr="{{ $class->hours }}" @if($class_id==$class->id) selected @endif>{{ $class->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label for="class_id" class="col-sm-3 control-label"> Schedule Start </label>
                                <div class="col-sm-9">
                                    <label id="start" style="margin-top:8px;color:red;font-size:15px;"></label>                                    
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green btn-icon" id="submit">
                    Save
                    <i class="entypo-check"></i>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('scripts')
<script src="{{ asset('assets/fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/fullcalendar/theme-chooser.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>
<script>

                                        $(document).ready(function () {

                                        initThemeChooser({

                                        init: function (themeSystem) {
                                        $('#calendar').fullCalendar({
                                        themeSystem: themeSystem,
                                                header: {
                                                left: 'prev,next today',
                                                        center: 'title',
                                                        right: 'agendaDay,listMonth'
                                                },
                                                defaultDate: "{{ date('Y-m-d') }}",
                                                defaultView: 'agendaDay',
                                                weekNumbers: true,
                                                navLinks: true, // can click day/week names to navigate views
                                                editable: false,
                                                eventLimit: true, // allow "more" link when too many events
                                                allDaySlot:false,
                                                eventColor: '#ed164f',
                                                eventColor: '#ed164f',
                                                selectable: true,
                                                selectHelper: true,
                                                select: function (start, end) {
                                                $('#myModal').modal({show: true});
                                                $('#form1')[0].reset();
                                                $('#start').html(moment(start).format("DD/MM/YYYY hh:mm:A"));
                                                $('#submit').off('click');
                                                $('#submit').on('click', function (e) {
                                                $('.loading-image-big').show();
                                                $('#myModal').modal('hide');
                                                e.preventDefault();
                                                var ID = $('#class_id').val();
                                                var hr = $('option:selected', '#class_id').attr("data-hr");
                                                end = $.fullCalendar.moment(start);
                                                end.add(hr, 'minutes');
                                                $.ajax({
                                                "url": '{{ url("$configM2/addSchedule") }}',
                                                        type: "POST",
                                                        async: true,
                                                        cache: false,
                                                        data: {
                                                        class_id: ID,
                                                                start: moment(start).format("HH:mm:ss"),
                                                                end: moment(end).format("HH:mm:ss"),
                                                                schedule_date: moment(start).format("YYYY-MM-DD"),
                                                                _token: '{{ csrf_token() }}'
                                                        },
                                                        success: function (data) {
                                                        if (data.error) {
                                                        toastr.error(data.error, "", opts2);
                                                        }
                                                        if (data.response) {
                                                        toastr.success(data.response, "", opts);
                                                        $('#myModal').modal('hide');
                                                        var eventData;
                                                        eventData = {
                                                        title: data.result.class_name,
                                                                start: data.result.start,
                                                                end: data.result.end,
                                                                id: data.result.id,
                                                                backgroundColor: '#0f9bd8',
                                                                borderColor: '#0f9bd8'
                                                        };
                                                        $('#calendar').fullCalendar('renderEvent', eventData, true);
                                                        }

                                                        },
                                                        complete: function () {
                                                        $('.loading-image-big').hide();
                                                        }
                                                });
                                                });
                                                $('#calendar').fullCalendar('unselect');
                                                },
                                                eventRender: function(event, element) {
                                                element.find(".fc-bg").css("pointer-events", "none");
                                                element.append("<div style='position:absolute;bottom:0px;right:0px;cursor:pointer;z-index:2;' ><button type='button' id='btnDeleteEvent' class='btn btn-block btn-danger btn-flat'><i class='fa fa-close'></i></button></div>");
                                                element.find("#btnDeleteEvent").off('click');
                                                element.find("#btnDeleteEvent").on('click', function () {
                                                if (confirm('{{ config('global.deleteConfirmation') }}')) {
                                                $('.loading-image-big').show();
                                                $.ajax({
                                                "url": '{{ url("$configM2/deleteSchedule") }}',
                                                        type: "POST",
                                                        async: true,
                                                        cache: false,
                                                        data: {
                                                        id: event.id,
                                                                _token: '{{ csrf_token() }}'
                                                        },
                                                        success: function (data) {
                                                        if (data.error) {
                                                        toastr.error(data.error, "", opts2);
                                                        }
                                                        if (data.response) {
                                                        toastr.success(data.response, "", opts);
                                                        $('#calendar').fullCalendar('removeEvents', event._id);
                                                        }

                                                        },
                                                        complete: function () {
                                                        $('.loading-image-big').hide();
                                                        }
                                                });
                                                }
                                                else{
                                                return false;
                                                }

                                                });
                                                },
                                                events: [
                                                        @foreach ($class_schedules as $class_schedule)
                                                {
                                                title:'{{ $class_schedule->class_name }}',
                                                        start: '{{ $class_schedule->start }}',
                                                        end: '{{ $class_schedule->end }}',
                                                        id: '{{ $class_schedule->id }}',
                                                        backgroundColor: '#0f9bd8',
                                                        borderColor: '#0f9bd8'
                                                },
                                                        @endforeach
                                                ]

                                        });
                                        },
                                                change: function (themeSystem) {
                                                $('#calendar').fullCalendar('option', 'themeSystem', themeSystem);
                                                }

                                        });
// Sample Toastr Notification
                                        var opts = {
                                        "closeButton": true,
                                                "debug": false,
                                                "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
                                                "toastClass": "sucess",
                                                "onclick": null,
                                                "showDuration": "300",
                                                "hideDuration": "1000",
                                                "timeOut": "5000",
                                                "extendedTimeOut": "1000",
                                                "showEasing": "swing",
                                                "hideEasing": "linear",
                                                "showMethod": "fadeIn",
                                                "hideMethod": "fadeOut"
                                        };
// Sample Toastr Notification
                                        var opts2 = {
                                        "closeButton": true,
                                                "debug": false,
                                                "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
                                                "toastClass": "error",
                                                "onclick": null,
                                                "showDuration": "300",
                                                "hideDuration": "1000",
                                                "timeOut": "5000",
                                                "extendedTimeOut": "8000",
                                                "showEasing": "swing",
                                                "hideEasing": "linear",
                                                "showMethod": "fadeIn",
                                                "hideMethod": "fadeOut"
                                        };
                                        //Weekly Modal
                                        $('#weeklySchedule').on('click', function (e) {
                                        $('#myWeeklyModal').modal({show: true});
                                        });
                                        $('#submit_weekly_record').on('click', function (e) {
                                        e.preventDefault();
                                        var ID = $('#weekly_class_id').val();
                                        var hr = $('option:selected', '#weekly_class_id').attr("data-hr");
                                        var start_date = $('#start_date').val();
                                        var end_date = $('#end_date').val();
                                        var week_day = $('option:selected', '#week_day').val();
                                        var start_time = $('#start_time').val();
                                        if (confirm('{{ config('global.deleteConfirmation') }}')) {
                                        $('#myWeeklyModal').modal('hide');
                                        $('.loading-image-big').show();
                                        $.ajax({
                                        "url": '{{ url("$configM2/addWeeklySchedule") }}',
                                                type: "POST",
                                                async: true,
                                                cache: false,
                                                data: {
                                                class_id: ID,
                                                        hour: hr,
                                                        start_date: start_date,
                                                        end_date: end_date,
                                                        week_day: week_day,
                                                        start_time: start_time,
                                                        _token: '{{ csrf_token() }}'
                                                },
                                                success: function (data) {
                                                   
                                                if (data.error) {
                                                toastr.error(data.error, "", opts2);
                                                }
                                                if (data.response) {
                                                toastr.success(data.response, "", opts);
                                                $('#myWeeklyModal').modal('hide');
                                                location.reload(); 
                                                }
                                                },
                                                complete: function () {
                                                $('.loading-image-big').hide();
                                                }
                                        });
                                        }
                                        else{
                                        return false;
                                        }
                                        });
                                        });
                                        // On change ID
                                        function GetSelectedTextValue(id) {

                                        $.ajax({
                                        type: "GET",
                                                async: true,
                                                "url": '{{ url("$configM2/manageSchedule") }}',
                                                data: {id: id},
                                                success: function (data) {
                                                $('#class_id').html('');
                                                $('#class_id').html(data.html);
                                                }
                                        });
                                        }

</script>
<script>
    $(function () {
    /*-------Date-----------*/
    $('#start_date').datetimepicker({
    format: 'DD/MM/YYYY',
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
    $('#end_date').datetimepicker({
    format: 'DD/MM/YYYY',
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
    $('#start_time').datetimepicker({
    format: 'hh:mm:A',
            toolbarPlacement: 'bottom'
    });
    });
</script>
@endsection