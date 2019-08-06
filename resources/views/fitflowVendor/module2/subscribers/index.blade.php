@extends('vendorLayouts.master')

@section('title')
Subscribers
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/minimal/_all.css') }}">

@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Subscribers
@endsection

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default" data-collapsed="0">


            <div class="col-sm-12 col-md-12 col-lg-12">

                <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                    <li class="{{ active($configM2.'/subscribers') }}">
                        <a href="{{ url($configM2.'/subscribers')  }}" >
                            <span class="visible-xs"><i class="entypo-home"></i></span>
                            <span class="hidden-xs">Active</span>
                        </a>
                    </li>
                    <li class="{{ active($configM2.'/archivedSubscribers') }}">
                        <a href="{{ url($configM2.'/archivedSubscribers')  }}" >
                            <span class="visible-xs"><i class="entypo-user"></i></span>
                            <span class="hidden-xs">Archived</span>
                        </a>
                    </li>

                </ul>

                <div class="tab-content">
                    <?php /* ?>
                     <div class="col-sm-12 margin10">
                        <div class="col-sm-6">
                            <label for="log" class="col-sm-2 control-label">Subscribers</label>

                            <div class="col-sm-9">
                                <select name="log" class="select2" data-allow-clear="true" id="log" onchange="GetSelectedTextValue()" >
                                    <option value="0">--Select Subscriber</option>
                                    @foreach ($Subscribers as $User)
                                    <option value="{{ $User->subscriber_id }}"> {{ $User->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                      <?php */ ?>
                    <div class="tab-pane active" id="active">

                        <div class="panel-body  table-responsive">
                            <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                            <table class="table table-bordered datatable" id="table-4">
                                <thead>
                                    <tr>
                                        <th class="col-sm-3">Subscriber</th>
                                        <th class="col-sm-3">Email ID</th>
                                        <th class="text-center col-sm-1">Mobile</th>
                                        <th class="text-center col-sm-1">Area</th>
                                        <th class="text-center col-sm-1">Gender</th>
                                        <th class="text-center col-sm-3">Actions</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>


                    </div>


                </div>


            </div>


            <div class="clear visible-xs"></div>


        </div>

    </div>
</div>



<!-- Modal 2 (Current Pacakge)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"   style="width: 35%;">
        <div class="modal-content"  id="current_package">


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
                    jQuery(document).ready(function ($) {
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
                                "url": '{{ url("$configM2/subscribers") }}',
                                 data: function (data) {
                                    data.id = $('#log').val();
                                },
                                complete: function () {
                                    $('.loading-image').hide();
                                }
                            },
                            columns: [
                                {data: 2, name: 'name'},
                                {data: 3, name: 'email', orderable: false, searchable: false, class: 'text-center'},
                                {data: 4, name: 'mobile', class: 'text-center'},
                                {data: 5, name: 'area', class: 'text-center'},
                                {data: 6, name: 'gender', class: 'text-center'},
                                {data: 9, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                            ],
                            order: [[2, 'desc']],
                            "fnDrawCallback": function (oSettings) {

                                /*----Current Package---*/
                                $('.current_package').on('click', function (e) {
                                    e.preventDefault();
                                    $('#current_package').html('');
                                    var ID = $(this).attr('data-val');
                                    $('.loading-image').show();
                                    $.ajax({
                                        type: "GET",
                                        async: true,
                                        "url": '{{ url("$configM2/subscribers") }}/'+ID + '/currentPackage',
                                        success: function (data) {
                                            $('#current_package').html(data.html);
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    });
                                });
                                /*------END----*/
                                /*----Current Booking---*/
                                $('.current_bookings').on('click', function (e) {
                                    e.preventDefault();
                                    $('#current_package').html('');
                                    var ID = $(this).attr('data-val');
                                    $('.loading-image').show();
                                    $.ajax({
                                        type: "GET",
                                        async: true,
                                        "url": '{{ url("$configM2/subscribers") }}/'+ID + '/currentBooking',
                                        success: function (data) {
                                            $('#current_package').html(data.html);
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    });
                                });
                                /*------END----*/

                                /*----Current Pacakge Payment Details---*/
                                $('.payment_details').on('click', function (e) {
                                    e.preventDefault();
                                    $('#current_package').html('');
                                    var ID = $(this).attr('data-val');
                                    $('.loading-image').show();
                                    $.ajax({
                                        type: "GET",
                                        async: true,
                                        "url": '{{ url("$configM2/subscribers") }}/'+ID + '/paymentDetails',
                                        success: function (data) {
                                            $('#current_package').html(data.html);
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    });
                                });
                                /*------END----*/
                                
                            },
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
                    
                     // On change trainer name
                    function GetSelectedTextValue() {
                        var $table4 = $("#table-4");
                        $table4.DataTable().draw();
                    }
                    
</script>



@endsection