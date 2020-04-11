@extends('trainerLayouts.master')

@section('title')
Attendance History
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

    <a href="{{ url('trainer/subscribers')  }}">Subscribers</a>
</li>
@endsection

@section('pageheading')
Attendance History - {{ ucfirst(trans($currentPackage->name)) }}
@endsection

   <div class="row">
        <div class="col-md-12">

            <div class="panel panel-dark" data-collapsed="0">
                    
                
        <div class="row margin10">
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            <label for="field-2" class="col-sm-12 control-label" style="font-size:14px;">Package Name</label>
                             <span for="field-2" class="col-sm-12 control-label">{{ $currentPackage->name_en }}</span>
                            
                        </div>
                         <div class="col-sm-2">
                            <label for="field-2" class="col-sm-12 control-label" style="font-size:14px;">Duration</label>
                             <span for="field-2" class="col-sm-12 control-label">{{ $currentPackage->num_days }} Days</span>
                            
                        </div>
                        <div class="col-sm-4">
                            <label for="field-2" class="col-sm-12 control-label" style="font-size:14px;">Date</label>
                             <span for="field-2" class="col-sm-12 control-label">{{ $currentPackage->start_date }} - {{ $currentPackage->end_date }}</span>
                            
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-info btn-xs" style="font-size:15px;">Attended {{ $totalAttend }}</button>                            
                        </div>
                         <div class="col-sm-2">
                             <button type="button" class="btn btn-danger btn-xs" style="font-size:15px;">Pending {{ $totalRemaining }}</button> 
                        </div>

                    </div>


                </div>
                
                <div class="panel-body  table-responsive">
                                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                                <table class="table table-bordered datatable" id="table-4">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-4 text-center ">DateTime</th>
                                            <th class="col-sm-7">Description</th>
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
                                var ID='{{ $id }}';
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
                                          url: "{{ url('trainer/subscribers/')}}/"+ID+ '/attendanceHistory',
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 1, name: 'date', class: 'text-center'},
                                        {data: 2, name: 'description_en'},
                                        {data: 3, name: 'status', class: 'text-center'}
                                    ],
                                    order: [[1, 'desc']],                                    
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