@extends('layouts.master')

@section('title')
Class Schedules
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
Class Schedules
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-default" data-collapsed="0">   
            <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                @if (Auth::user()->hasRolePermission('classes'))
                <li class="{{ active('admin/module2ClassSchedules') }}">
                    <a href="{{ url('admin/module2ClassSchedules')  }}" >
                        <span class="visible-xs"><i class="entypo-user"></i></span>
                        <span class="hidden-xs">{{ ucwords(str_replace("-", " ", config('global.M2'))) }}</span>
                    </a>
                </li>
                <li class="{{ active('admin/module3ClassSchedules') }}">
                    <a href="{{ url('admin/module3ClassSchedules')  }}" >
                        <span class="visible-xs"><i class="entypo-user"></i></span>
                        <span class="hidden-xs">{{ ucwords(str_replace("-", " ", config('global.M3'))) }}</span>
                    </a>
                </li>
                @endif

            </ul>

            <div class="col-sm-12 margin10">
                <div class="col-sm-6">
                    <label for="Vendor_id" class="col-sm-2 control-label">Vendors</label>

                    <div class="col-sm-9">
                        <select name="vendor_id" class="select2" data-allow-clear="true" id="vendor_id"   onchange="GetSelectedVendorValue(this.value);">
                            <option value="0">--Select Vendor</option>
                            @foreach ($Vendors as $Vendor)
                            <option value="{{ $Vendor->id }}"> {{ $Vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label for="class_id" class="col-sm-3 control-label"> Classes</label>
                    <div class="col-sm-9">
                        <select name="class_id" class="select2" data-allow-clear="true" id="class_id" style="padding:6px 10px;" onchange="GetSelectedClassValue(this.value);">
                            <option value="0">--Select--</option>
                            @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;display:none;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <div id="getSchedule">
                    <div id='calendar'></div>
                </div>


                </table>
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
                                            "url": '{{ url("admin/classDetail") }}/' + calEvent.id + '/' + 3,
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
                                    title:'{{ $class_schedule->class_name }}\n Total Seats:- {{ $class_schedule->total_seats }}\n Total Booked:- {{ is_null($class_schedule->app_booked)?0:$class_schedule->app_booked }}',
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
    function GetSelectedClassValue(val){
    $('.loading-image').show();
    var val2 = $('#vendor_id').val();
    $.ajax({
    type: "GET",
            async: true,
            "url": '{{ url("admin/module3ClassSchedules") }}/' + val + '/' + val2, //val:class_id;val2:vendor_id
            success: function (data) {
            $('#getSchedule').html('');
            $('#getSchedule').html(data.html);
            },
            complete: function () {
            $('.loading-image').hide();
            }
    });
    }
    /*------END----*/

    /*----Vendor change---*/
    function GetSelectedVendorValue(val2){
    $('.loading-image').show();
    var val = $('#class_id').val();
    $.ajax({
    type: "GET",
            async: true,
            "url": '{{ url("admin/module3ClassSchedules") }}/' + val + '/' + val2, //val:class_id;val2:vendor_id
            success: function (data) {
            $('#getSchedule').html('');
            $('#class_id').html('').html(data.classes);
            $('#getSchedule').html(data.html);
            },
            complete: function () {
            $('.loading-image').hide();
            }
    });
    }
    /*------END----*/
</script>

@endsection