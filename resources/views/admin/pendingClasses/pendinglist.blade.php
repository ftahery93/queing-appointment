@extends('layouts.master')

@section('title')
{{ ucfirst($vendorName->name) }} - Pending  Classes 
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
@if($EditAccess!=1)
<style>
    table tr th:last-child, table tr td:last-child{display:none;}
</style>
@endif

@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/pendingVendorClasses')  }}">Pending List</a>
</li>
@endsection

@section('pageheading')
{{ ucfirst($vendorName->name) }} - Pending  Classes 
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-default" data-collapsed="0">
            <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                <li class="active">
                    <a href="{{ url('admin/pendingClasses').'/'.$vendor_id  }}" >
                        <span class="visible-xs"><i class="entypo-home"></i></span>
                        <span class="hidden-xs">Pending Classes</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('admin/pendingCommission').'/'.$vendor_id  }}" >
                        <span class="visible-xs"><i class="entypo-user"></i></span>
                        <span class="hidden-xs">Pending Classes Commission</span>
                    </a>
                </li>

            </ul>
            <div class="panel-heading">

                <div class="panel-options padding10">
                    <button type="button" class="btn btn-green btn-icon" id="save" onclick="ConfirmDelete();">
                        Save
                        <i class="entypo-check"></i>
                    </button>

                </div>
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th class="col-sm-1">Class Name</th>
                            <th class="text-center col-sm-1"> Total Seats</th>
                            <th class="text-center col-sm-2"> {{ $appTitle->title }} Seats</th>
                            <th class="text-center col-sm-1"> Price</th>
                            <th class="text-center col-sm-2">Reason</th>
                            <th class="text-center  col-sm-2">Status</th>
                            <th class="text-center  col-sm-2">Action</th>
                        </tr>
                    </thead>


                </table>
            </div>


        </div>

    </div>
</div>

<!-- Modal 2 (Payment Details)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"   style="width: 25%;">
        <div class="modal-content"  id="previousDetail">


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('scripts')
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
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
                                        "url": '{{ url("admin/pendingClasses/$vendor_id") }}',
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'ID', orderable: false, searchable: false, class: 'text-center'},
                                        {data: 1, name: 'name_en'},
                                        {data: 2, name: 'temp_num_seats', orderable: false, class: 'text-center'},
                                        {data: 3, name: 'temp_fitflow_seats', orderable: false, class: 'text-center'},
                                        {data: 4, name: 'temp_price', orderable: false, class: 'text-center'},
                                        //{data: 5, name: 'temp_commission_perc', orderable: false, class: 'text-center'},
                                        {data: 6, name: 'reason', orderable: false, class: 'text-center'},
                                        {data: 7, name: 'id', orderable: false, searchable: false, class: 'text-center'},
                                        {data: 8, name: 'action', orderable: false, searchable: false, class: 'text-center'},
                                    ],
                                    order: [[0, 'desc']],
                                    "fnDrawCallback": function (oSettings) {
                                        /*----Previous  Details---*/
                                        $('.previousDetail').on('click', function (e) {
                                            e.preventDefault();
                                            $('#previousDetail').html('');
                                            var ID = $(this).attr('data-val');
                                            $('.loading-image').show();
                                            $.ajax({
                                                type: "GET",
                                                async: true,
                                                "url": '{{ url("admin/pendingClasses/previousDetail/")}}/' + ID,
                                                success: function (data) {
                                                    $('#previousDetail').html(data.html);
                                                },
                                                complete: function () {
                                                    $('.loading-image').hide();
                                                }
                                            });
                                        });
                                        /*------END----*/
                                        $('.number_only').keypress(function (e) {
                                            return isNumbers(e, this);
                                        });
                                        $('input.icheck-14').iCheck({
                                            checkboxClass: 'icheckbox_polaris',
                                            radioClass: 'iradio_polaris'
                                        });
                                        $('#check-all').on('ifChecked', function (event) {
                                            $('.check').iCheck('check');
                                            return false;
                                        });
                                        $('#check-all').on('ifUnchecked', function (event) {
                                            $('.check').iCheck('uncheck');
                                            return false;
                                        });
// Removed the checked state from "All" if any checkbox is unchecked
                                        $('#check-all').on('ifChanged', function (event) {
                                            if (!this.changed) {
                                                this.changed = true;
                                                $('#check-all').iCheck('check');
                                            } else {
                                                this.changed = false;
                                                $('#check-all').iCheck('uncheck');
                                            }
                                            $('#check-all').iCheck('update');
                                        });
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
                            });</script>
<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        $('input.icheck-14').iCheck({
            checkboxClass: 'icheckbox_polaris',
            radioClass: 'iradio_polaris'
        });
        /*---CheckAll---*/
        $('#check-all').on('ifChecked', function (event) {
            $('.check').iCheck('check');
            return false;
        });
        $('#check-all').on('ifUnchecked', function (event) {
            $('.check').iCheck('uncheck');
            return false;
        });
// Removed the checked state from "All" if any checkbox is unchecked
        $('#check-all').on('ifChanged', function (event) {
            if (!this.changed) {
                this.changed = true;
                $('#check-all').iCheck('check');
            } else {
                this.changed = false;
                $('#check-all').iCheck('uncheck');
            }
            $('#check-all').iCheck('update');
        });
        /*------END----*/

    });
    /*---Confirmation--before--submit-*/
    function ConfirmDelete() {
        var $table4 = $("#table-4");

        if (confirm('{{ config('global.deleteConfirmation') }}')) {
            var json = [];
            $('#table-4').find('tr').each(function () {
                var id = $(this).attr('id');
                var row = {};


                row['temp_commission_perc'] = $('.temp_commission_perc', this).val();
                row['temp_price'] = $('.temp_price', this).val();
                row['ids'] = $('.ids', this).val();
                row['class_status'] = $('input[name="class_status' + id + '"]:checked', this).val();
                row['reason'] = $('.reason', this).val();
                json[id] = row;
            });
            // var ids = $('.check').serializeArray();
            //var gym_seats = $('.gym_seats').serializeArray();
            $.ajax({
                type: "POST",
                async: true,
                "url": '{{ url("admin/pendingClasses/editClasses") }}',
                data: {jsonData: json, vendorName: '{{ $vendorName->name }}', vendorID: '{{ $vendor_id }}', _token: '{{ csrf_token() }}'},
                success: function (data) {
                    if (data.response) {
                        $table4.DataTable().ajax.reload(null, false);
                        toastr.success(data.response, "", opts);
                    }
                    if (data.error) {
                        $table4.DataTable().ajax.reload(null, false);
                        toastr.error(data.error, "", opts2);
                    }
                }
            });
        } else {
            return false;
        }


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
            "hideDuration": "1000000",
            "timeOut": "50000000",
            "extendedTimeOut": "8000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    }
    /*------END----*/

    function isNumbers(evt, element)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (
                (charCode != 46 || $(element).val().indexOf('.') != -1) && // “.�? CHECK DOT, AND ONLY ONE.
                (charCode > 57))
            return false;
        return true;
    }

</script>

@endsection