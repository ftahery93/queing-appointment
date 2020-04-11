@extends('trainerLayouts.master')

@section('title')
Archived Attendance History
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">

@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('trainer/archivedSubscribers')  }}">Archived</a>
</li>
@endsection

@section('pageheading')
Archived Attendance History - {{ ucfirst(trans($username->name)) }}
@endsection

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-dark" data-collapsed="0">                    

            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-3">Package Name</th>
                            <th class="col-sm-1 text-center">No. Classes</th>
                            <th class="col-sm-1 text-center">Start Date</th>
                            <th class="col-sm-1 text-center">End Date</th>
                            <th class="col-sm-2 text-center">DateTime</th>
                            <th class="col-sm-3">Description</th>
                            <th class="text-center col-sm-1">Status</th>
                        </tr>
                    </thead>

                </table>
            </div>

            <div class="clear visible-xs"></div>


        </div>

    </div>
</div>

@endsection

@section('scripts')

<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
jQuery(document).ready(function ($) {
    var ID = '{{ $id }}';
    var $table4 = jQuery("#table-4");
    $table4.DataTable({
        dom: 'lBfrtip',
        "stateSave": true,
        processing: true,
        serverSide: true,
        ordering: true,
        language: {
            processing: "<img src='{{ asset('assets/images/loader-1.gif') }}'>"
        },
        "ajax": {
            "type": "GET",
            url: "{{ url('trainer')}}/"+ID+ '/archivedAttendanceHistory',
            complete: function () {
                $('.loading-image').hide();
            }
        },
        columns: [
            {data: 0, name: 'name_en'},
            {data: 1, name: 'num_points', class: 'text-center'},
            {data: 2, name: 'start_date', class: 'text-center'},
            {data: 3, name: 'end_date', class: 'text-center'},
            {data: 4, name: 'date', class: 'text-center'},
            {data: 5, name: 'description_en'},
            {data: 6, name: 'status', class: 'text-center'}
        ],
        order: [[2, 'desc']],
        buttons: [
            //'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
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
});</script>
<script type="text/javascript">
    function showAjaxModal()
    {

        jQuery('#myModal3').modal('show', {backdrop: 'static'});

    }
</script>

@endsection