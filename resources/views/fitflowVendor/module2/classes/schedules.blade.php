@extends('vendorLayouts.master')

@section('title')
Schedules
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/fullcalendar/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
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


</style>
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Schedules
@endsection

<div class="row">
    <div class="col-md-12">
        @include('vendorLayouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">


            <div class="panel-body">

                <div class="row">
                    <div class="form-group  col-sm-12">
                        <div class="col-sm-6">
                            <label for="class_id" class="col-sm-3 control-label"> Classes</label>
                            <div class="col-sm-9">
                                <select name="class_id" class="select2" data-allow-clear="true" id="class_id" style="padding:6px 10px;" onchange="GetSelectedTextValue(this.value);">
                                    <option value="0">--Select--</option>
                                    @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                </div>
                <div id="getSchedule">
                    <div id='calendar'></div>
                </div>

            </div>

        </div>

    </div>
</div>
<!-- Modal 1 (Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"  style="width: 30%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Class Details</h4>
                <div class="loading-image-modal" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
            </div>
            <div class="modal-body" id="classDetails">
           
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
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
                                            defaultView: 'listMonth',
                                            weekNumbers: true,
                                            navLinks: true, // can click day/week names to navigate views
                                            editable: false,
                                            eventLimit: true, // allow "more" link when too many events
                                            allDaySlot:false,
                                            eventColor: '#ed164f',
                                            eventColor: '#ed164f',
                                            selectable: true,
                                            selectHelper: true,
                                            eventClick: function(calEvent, jsEvent, view, event) {
                                            $('#myModal').modal({show: true});
                                            $('#classDetails').html('');
                                            $('.loading-image-modal').show();
                                            $.ajax({
                                            type: "GET",
                                                    async: true,
                                                    "url": '{{ url("$configM2/classDetail") }}/' + calEvent.id,
                                                    success: function (data) {                                                    
                                                    $('#classDetails').html(data.html);
                                                    },
                                                    complete: function () {
                                                    $('.loading-image-modal').hide();
                                                    }
                                            });
                                            },
                                            events: [
                                                    @foreach ($class_schedules as $class_schedule)
                                            {
                                            title:'{{ $class_schedule->class_name }}\n Total Seats:- {{ $class_schedule->total_seats }}\n Total Booked:- {{ is_null($class_schedule->booked)?0:$class_schedule->booked }}',
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
                                    });</script>
<script>

    /*----Class change---*/
    function GetSelectedTextValue(val){

    $.ajax({
    type: "GET",
            async: true,
            "url": '{{ url("$configM2/schedules") }}/' + val,
            success: function (data) {
            $('#getSchedule').html('');
            $('#getSchedule').html(data.html);
            }
    });
    }
    /*------END----*/
</script>

@endsection