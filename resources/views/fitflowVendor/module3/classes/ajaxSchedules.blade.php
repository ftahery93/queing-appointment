
<div id='calendar'></div>
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
                    "url": '{{ url("$configM2/m3/classDetail") }}/' + calEvent.id,
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
    });

</script>
