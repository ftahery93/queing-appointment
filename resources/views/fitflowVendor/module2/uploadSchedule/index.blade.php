@extends('vendorLayouts.master')

@section('title')
Upload Schedule
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">


@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Upload Schedule
@endsection
 
<div class="row">
    <div class="col-md-12">
        @include('vendorLayouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">


            <div class="panel-body  table-responsive">
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-8">Title</th>
                            <th class="text-center col-sm-2">Created On</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>


                </table>
            </div>

        </div>

    </div>
</div>
<!-- Modal 1 (Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"  style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Uploaded List</h4>
            </div>
            <div class="modal-body">
                <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table border_0" id="importedfileslist">

                    <thead>
                        <tr>
                            <th class="col-sm-5"><b>Imported File Name</b></th>
                            <th class="col-sm-5 text-center"><b>Download Imported Data</b></th>
                            <th class="col-sm-2"><b>Created On</b></th>
                        </tr>                            
                    </thead>
                    <tbody>

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

<!-- Modal 2(Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="upload_schedule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 25%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Update Database Records</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  id="form1" >
                    <input type="hidden" name="table_id" value="" id="table_id">
                    <p class="text-danger text-center">All field is required and it should be letter only.</p>
                     <div class="row"> 
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="branch_name" class="col-sm-7 control-label">Branch Name <span style="color:red;">*</span></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="branch_name" autocomplete="off"  value="" name="branch_name">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row"> 
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="class_name" class="col-sm-7 control-label">Class Name <span style="color:red;">*</span></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="class_name" autocomplete="off"  value="" name="class_name">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">    
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="start_time" class="col-sm-7 control-label">Start Time <span style="color:red;">*</span></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="start_time" autocomplete="off"  value="" name="start_time">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">    
                        <div class="form-group  col-sm-12">
                            <div class="col-sm-12">
                                <label for="end_time" class="col-sm-7 control-label">Schedule Date<span style="color:red;">*</span></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="schedule_date" autocomplete="off"  value="" name="schedule_date">
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
                <button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal 3 (Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"  style="width:25%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Excel Index</h4>
            </div>
            <div class="modal-body">
                <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table border_0" id="imported_index">

                    <thead>
                        <tr>
                            <th class="col-sm-5"><b>Column Name</b></th>
                            <th class="col-sm-5 text-center"><b>Excel Index Name</b></th>
                        </tr>                            
                    </thead>
                    <tbody>

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

<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        var $table4 = jQuery("#table-4");
                        $table4.DataTable({
                            "stateSave": true,
                            processing: true,
                            serverSide: true,
                            ajax: '{{ url("$configM2/uploadSchedule/") }}',
                            language: {
                                processing: "<img src='{{ asset('assets/images/loader-1.gif') }}'>"
                            },
                            columns: [
                                {data: 0, name: 'table_name'},
                                {data: 1, name: 'created_at', class: 'text-center'},
                                {data: 4, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                            ],
                            order: [[1, 'ASC']],
                            "fnDrawCallback": function (oSettings) {

                                /*----Import list Update---*/
                                $('.importedlist').on('click', function (e) {
                                    e.preventDefault();
                                    var ID = $(this).attr('data-val');
                                    $('.loading-image').show();
                                    $.ajax({
                                        type: "GET",
                                        async: true,
                                        "url": '{{ url("$configM2/uploadSchedule/imported_list") }}/' + ID,
                                        success: function (data) {
                                            $('#importedfileslist tbody').html(data.html);
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    });
                                });
                                /*------END----*/
                                /*----Excel Index---*/
                                $('.imported_index').on('click', function (e) {
                                    e.preventDefault();
                                    var ID = $(this).attr('data-val');
                                    $('.loading-image').show();
                                    $.ajax({
                                        type: "GET",
                                        async: true,
                                        "url": '{{ url("$configM2/uploadSchedule/excelindex") }}/' + ID,
                                        success: function (data) {
                                            $('#imported_index tbody').html(data.html);
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    });
                                });
                                /*------END----*/
                                /*----Import Data Fields---*/
                                $('.import_datafields').on('click', function (e) {
                                    e.preventDefault();
                                    $('#form1')[0].reset();
                                    var ID = $(this).attr('data-val');
                                    $('#table_id').val(ID);
                                });

                                $('#submit').on('click', function (e) {
                                    e.preventDefault();
                                    //var var_data = $("#form1").serialize();
                                    var table_id = $('#table_id').val();
                                    var class_name = $('#class_name').val();
                                     var branch_name = $('#branch_name').val();
                                    var start_time = $('#start_time').val();
                                    var schedule_date = $('#schedule_date').val();
                                    $.ajax({
                                        type: "PUT",
                                        async: true,
                                        "url": '{{ url("$configM2/uploadSchedule/updateFields") }}',
                                        data: {table_id: table_id, branch_name: branch_name, class_name: class_name, start_time: start_time, schedule_date: schedule_date,_token: '{{ csrf_token() }}'},
                                        success: function (data) {
                                            if (data.response) {
                                                $table4.DataTable().ajax.reload(null, false);
                                                toastr.success(data.response, "", opts);
                                                $('#upload_schedule,.modal-backdrop.in').css("display", "none");
                                            }
                                            if (data.error) {
                                                toastr.error(data.error, "", opts2);
                                            }
                                        }
                                    });
                                });
                                /*------END----*/
                            },
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

@endsection