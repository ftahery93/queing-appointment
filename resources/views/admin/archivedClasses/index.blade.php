@extends('layouts.master')

@section('title')
Archived Classes
@endsection

@section('css')
<!-- Imported styles on this page -->
<link href='{{ asset('assets/fullcalendar/fullcalendar.min.css') }}' rel='stylesheet' />
<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">

<style>

    #top,
    #calendar.fc-unthemed,#calendar2.fc-unthemed {
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


    #calendar,#calendar2  {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 10px;
    }
    

</style>
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Archived Classes
@endsection
 <div class="row">

        <div class="col-md-12">

            <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                <li class="active">
                    <a href="#vendor" data-toggle="tab">
                        <span class="visible-xs"><i class="entypo-home"></i></span>
                        <span class="hidden-xs">Vendor Classes</span>
                    </a>
                </li>
                <li>
                    <a href="#fitflow" data-toggle="tab">
                        <span class="visible-xs"><i class="entypo-user"></i></span>
                        <span class="hidden-xs">Fitflow Classes</span>
                    </a>
                </li>

            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="vendor">

                    <div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <label for="field-2" class="col-sm-2 control-label">Vendors</label>

                                    <div class="col-sm-9">
                                        <select name="permission" class="select2" data-allow-clear="true" >
                                            <option value="">--Select Vendors</option>
                                            <option value="1"> Oxygen Gym</option>
                                            <option value="0"> Platinum Gym</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 margin10">

                                <div id='calendar'></div>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="tab-pane" id="fitflow">
                        <div class="row">
                            
                            <div class="col-md-12 margin10">

                                <div id='calendar2'></div>

                            </div>
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
                </div>
                <div class="modal-body">
                    <table class="table border_0">

                        <tbody>
                            <tr>
                                <td><b>Class Name</b></td>
                                <td>Yoga Class</td>
                            </tr>
                            <tr>
                                <td><b>Vendor Name</b></td>
                                <td>Oxygen Gym</td>
                            </tr>
                            <tr>
                                <td><b>Activity</b></td>
                                <td>Yoga</td>
                            </tr>
                            <tr>
                                <td><b>Class Time</b></td>
                                <td>3:00 PM</td>
                            </tr>
                            <tr>
                                <td><b>No.of Seats</b></td>
                                <td>25</td>
                            </tr>
                            <tr>
                                <td><b>Available Seats</b></td>
                                <td>5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
     <!-- Modal Fitflow (Ajax Modal)-->
    <!-- Modal -->
    <div class="modal fade" id="myModal_fitflow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog"  style="width: 30%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h4 class="modal-title">Class Details</h4>
                </div>
                <div class="modal-body">
                    <table class="table border_0">

                        <tbody>
                            <tr>
                                <td><b>Class Name</b></td>
                                <td>Yoga Class</td>
                            </tr>
                            <tr>
                                <td><b>Vendor Name</b></td>
                                <td>Oxygen Gym</td>
                            </tr>
                            <tr>
                                <td><b>Activity</b></td>
                                <td>Yoga</td>
                            </tr>
                            <tr>
                                <td><b>Class Time</b></td>
                                <td>3:00 PM</td>
                            </tr>
                            <tr>
                                <td><b>Total Seats</b></td>
                                <td>25</td>
                            </tr>
                             <tr>
                                <td><b>Fitflow Seats</b></td>
                                <td>10</td>
                            </tr>
                            <tr>
                                <td><b>Available Seats</b></td>
                                <td>3</td>
                            </tr>
                        </tbody>
                    </table>
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

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('assets/fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/fullcalendar/theme-chooser.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>

<script>

    $(document).ready(function () {

        initThemeChooser({

            init: function (themeSystem) {
                $('#calendar').fullCalendar({
                    themeSystem: themeSystem,
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay,listMonth'
                    },
                    defaultDate: '2017-11-01',
                    defaultView: 'agendaWeek',
                    weekNumbers: true,
                    navLinks: true, // can click day/week names to navigate views
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    eventColor: '#ed164f',
                    eventColor: '#ed164f',
                    eventRender: function (event, element) {
                        element.attr('data-toggle', "modal");
                        element.attr('data-target', "#myModal");
                        element.attr('href', "/details");
                    },
                    events: [
                        {
                            title: 'Yoga Class',
                             start: '2017-11-10',
                            end: '2017-11-15',
                            //url: 'http://google.com/',
                            backgroundColor: '#ea3d49',
                            borderColor: '#ea3d49'
                        },
                        {
                            title: 'Boxing Class',
                            start: '2017-11-07',
                            end: '2017-11-10',
                            //url: 'http://google.com/',
                            backgroundColor: '#0fbbbd',
                            borderColor: '#0fbbbd'
                        },
                        {
                            title: 'Yoga class',
                             start: '2017-12-18',
                            end: '2017-12-19',
                            backgroundColor: '#0f9bd8',
                            textColor: '#000',
                            //url: 'http://google.com/',
                            borderColor: '#0f9bd8'
                        }
                    ]
                });
            },

            change: function (themeSystem) {
                $('#calendar').fullCalendar('option', 'themeSystem', themeSystem);
            }

        });
        
        //Fitflow Calendar
             initThemeChooser({

            init: function (themeSystem) {
                $('#calendar2').fullCalendar({
                    themeSystem: themeSystem,
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay,listMonth'
                    },
                   defaultDate: '2017-11-01',
                    defaultView: 'agendaWeek',
                    weekNumbers: true,
                    navLinks: true, // can click day/week names to navigate views
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    eventColor: '#ed164f',
                    eventColor: '#ed164f',
                    eventRender: function (event, element) {
                        element.attr('data-toggle', "modal");
                        element.attr('data-target', "#myModal_fitflow");
                        element.attr('href', "/details");
                    },
                    events: [
                        {
                            title: 'Boxing Class',
                            start: '2017-11-07',
                            end: '2017-11-10',
                            //url: 'http://google.com/',
                            backgroundColor: '#e78e24',
                            borderColor: '#e78e24'
                        },
                        {
                            title: 'Swimming Class',
                            start: '2017-12-07',
                            end: '2017-12-10',
                            //url: 'http://google.com/',
                            backgroundColor: '#ea3d49',
                            borderColor: '#ea3d49'
                        },
                        {
                            title: 'Yoga class',
                            start: '2017-12-12',
                            end: '2017-12-15',
                            backgroundColor: '#ed164f',
                            textColor: '#000',
                            //url: 'http://google.com/',
                            borderColor: '#ed164f'
                        }
                    ]
                });
            },

            change: function (themeSystem) {
                $('#calendar2').fullCalendar('option', 'themeSystem', themeSystem);
            }

        });

    });

</script>
<script type="text/javascript">
    function showAjaxModal()
    {

        jQuery('#modal-7').modal('show', {backdrop: 'static'});

    }
</script>

@endsection