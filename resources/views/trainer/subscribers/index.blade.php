@extends('trainerLayouts.master')

@section('title')
Subscribers
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/minimal/_all.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
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
                    <li class="{{ active('trainer/subscribers') }}">
                        <a href="{{ url('trainer/subscribers')  }}" >
                            <span class="visible-xs"><i class="entypo-home"></i></span>
                            <span class="hidden-xs">Active</span>
                        </a>
                    </li>
                    <li class="{{ active('trainer/archivedSubscribers') }}">
                        <a href="{{ url('trainer/archivedSubscribers')  }}" >
                            <span class="visible-xs"><i class="entypo-user"></i></span>
                            <span class="hidden-xs">Archived</span>
                        </a>
                    </li>

                </ul>

                <div class="tab-content">
<!--                     <div class="col-sm-12 margin10">
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
                    </div>-->
                    <div class="tab-pane active" id="active">

                        <div class="panel-body  table-responsive">
                            <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                            <table class="table table-bordered datatable" id="table-4">
                                <thead>
                                    <tr>
                                         <th class="col-sm-3">Subscriber</th>
                                        <th class="col-sm-2">Email ID</th>
                                        <th class="text-center col-sm-1">Mobile</th>
                                        <th class="text-center col-sm-1">Area</th>
                                        <th class="text-center col-sm-1">Gender</th>
                                        <th class="text-center col-sm-1">No. of Classes</th>
                                        <th class="text-center col-sm-1">Attendance</th>
                                        <th class="text-center col-sm-2">Actions</th>
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

<!-- Modal 3(Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 25%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Add Attendance</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form1" >

                    <div class="row">                        
                        <input type="hidden" name="subscribed_package_id" value="" id="subscribed_package_id">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="date" class="col-sm-4 control-label">DateTime <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control datetimepicker" id="date" autocomplete="off"  value="" name="date">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="status" class="col-sm-4 control-label">Status <span style="color:red;">*</span></label>
                                <div class="col-sm-8">
                                    <select name="status" class="" data-allow-clear="true" id="status" >
                                        <option value="">--Select Status</option>
                                        <option value="1"> Attended</option>
                                        <option value="0"> Not Attend</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-12">
                                <label for="description_en" class="col-sm-4 control-label">Reason</label>
                                <div class="col-sm-8">
                                    <textarea  class="form-control resize" name="description_en" id="description_en" ></textarea>

                                </div>
                            </div>
                        </div>
                    </div>

                    <hr/>


                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green btn-icon" id="submit">
                    Save
                    <i class="entypo-check"></i>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal 2 (Current Pacakge)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"   style="width: 25%;">
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
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>
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
                                "url": '{{ url("trainer/subscribers") }}',
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
                                {data: 6, name: 'gender', orderable: false, searchable: false, class: 'text-center'},
                                {data: 8, name: 'num_class', orderable: false, searchable: false, class: 'text-center'},
                                {data: 9, name: 'attendance', orderable: false, searchable: false, class: 'text-center text-success bold'},
                                {data: 10, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                            ],
                            order: [[0, 'desc']],
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
                                        url: "{{ url('trainer/subscribers/')}}/"+ID + '/currentPackage',
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
                                        url: "{{ url('trainer/subscribers/')}}/"+ID + '/paymentDetails',
                                        success: function (data) {
                                            $('#current_package').html(data.html);
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    });
                                });
                                /*------END----*/
                                /*----Add Attendance---*/
                                $('.add_attendance').on('click', function (e) {
                                    e.preventDefault();
                                    $('#form1')[0].reset();
                                    var ID = $(this).attr('data-val');
                                    $('#subscribed_package_id').val(ID);
                                });

                                $('#submit').on('click', function (e) {
                                    e.preventDefault();
                                    var ID = $('#subscribed_package_id').val();
                                    var date = $('#date').val();
                                    var status = $('#status').val();
                                    var description_en = $('#description_en').val();
                                    $.ajax({
                                        type: "POST",
                                        async: true,
                                        url: "{{ url('trainer/subscribers/create')  }}",
                                        data: {subscribed_package_id: ID, date: date, status: status, description_en: description_en, _token: '{{ csrf_token() }}'},
                                        success: function (data) {
                                            if (data.response) {
                                                $table4.DataTable().ajax.reload(null, false);
                                                toastr.success(data.response, "", opts);
                                                $('#myModal2,.modal-backdrop.in').css("display", "none");
                                            }
                                            if (data.error) {
                                                toastr.error(data.error, "", opts2);
                                            }
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
</script>

<script>
    // On change trainer name
                    function GetSelectedTextValue() {
                        var $table4 = $("#table-4");
                        $table4.DataTable().draw();
                    }
    $(function () {
        /*-------Date-----------*/
        $('#date').datetimepicker({
            format: 'DD/MM/YYYY hh:mm:A',
            toolbarPlacement: 'bottom'
        });
    });
</script>

@endsection