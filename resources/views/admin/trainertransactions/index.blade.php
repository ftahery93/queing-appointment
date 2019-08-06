@extends('layouts.master')

@section('title')
{{ ucfirst($trainerName->name) }} -  Transactions
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
@if($EditAccess!=1)
<style>
    table tr th:last-child, table tr td:last-child{display:none;}
</style>
@endif
@if($DeleteAccess!=1)
<style>
    table tr th:first-child, table tr td:first-child{display:none;}
</style>
@endif

@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('/admin/trainers') }}">Trainers</a>
</li>
@endsection

@section('pageheading')
{{ ucfirst($trainerName->name) }} -  Transactions
@endsection
<form class="form" role="form" method="POST" action="{{ url('admin/trainertransactions/delete')  }}" >  
    {{ csrf_field() }} 
    <div class="row">
        <div class="col-md-12">
            @include('layouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">


                <div class="panel-heading">

                    <div class="panel-options">
                        @if ($CreateAccess==1)
                        <a href="{{ url('admin/trainertransactions').'/'.$trainer_id.'/create'  }}" class="margin-top0">
                            <button type="button" class="btn btn-default btn-icon">
                                Add Record
                                <i class="entypo-plus padding10"></i>
                            </button>
                        </a>
                        @endif

                        @if ($DeleteAccess==1)
                       <button Onclick="return ConfirmDelete();" type="button" class="btn btn-red btn-icon">
                            Delete
                            <i class="entypo-cancel"></i>
                        </button>
<!--                        <a href="/admin/transactions/trashedlist" class="margin-top0">
                            <button type="button" class="btn btn-orange btn-icon">
                                Trashed List
                                <i class="entypo-ccw padding10"></i>
                            </button>
                        </a>-->
                        @endif

                    </div>
                </div>

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="text-center"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>
                                <th class="col-sm-2">Name</th>
                                <th class="col-sm-2">Reference No.</th>
                                <th class="col-sm-2">Transferred Amount</th>
                                <th class="col-sm-2">Payment Mode</th>
                                <th class="text-center col-sm-2">Transferred Date</th>
                                <th class="text-center col-sm-2">Actions</th>
                            </tr>
                        </thead>


                    </table>
                </div>

            </div>

        </div>
    </div>
</form>
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
                                        "url": '{{ url("admin/$trainer_id/trainertransactions") }}',
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'id', class: 'text-center checkbox_padding'},
                                        {data: 1, name: 'name'},
                                        {data: 2, name: 'reference_num', orderable: false, searchable: false, class: 'text-center'},
                                        {data: 3, name: 'transferred_amount', orderable: false, searchable: false, class: 'text-center'},
                                        {data: 4, name: 'name_en', orderable: false, searchable: false, class: 'text-center'},
                                        {data: 5, name: 'transferred_date', class: 'text-center'},
                                        {data: 6, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                                    ],
                                    order: [[5, 'desc']],
                                    "fnDrawCallback": function (oSettings) {
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
                                        /*----Status Update---*/
                                        $('.status').on('click', function (e) {
                                            e.preventDefault();
                                            var ID = $(this).attr('sid');
                                            var Value = $(this).attr('value');
                                            $.ajax({
                                                type: "PATCH",
                                                async: true,
                                                url: "{{ url('admin/trainertransactions')}}/"+ID,
                                                data: {id: ID, status: Value, _token: '{{ csrf_token() }}'},
                                                success: function (data) {
                                                    $table4.DataTable().ajax.reload(null, false);
                                                    toastr.success(data.response, "", opts);
                                                }
                                            });
                                        });
                                        /*------END----*/
                                        /*----sendCredential Email---*/
                                        $('.transactionEmail').on('click', function (e) {
                                            e.preventDefault();
                                            var ID = $(this).attr('data-id');
                                            $.ajax({
                                                type: "GET",
                                                async: true,
                                                url: "{{ url('admin/trainertransactions')}}/"+ID + '/transactionEmail',
                                                data: {id: ID},
                                                success: function (data) {
                                                    $table4.DataTable().ajax.reload(null, false);
                                                    toastr.success(data.response, "", opts);
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
     /*---On Delete All Confirmation---*/
    function ConfirmDelete() {
        var chkId = '';
        $('.check:checked').each(function () {
            chkId = $(this).val();
        });
        if (chkId == '') {
            alert('{{ config('global.deleteCheck') }}');
            return false;
        } else {
            if (confirm('{{ config('global.deleteConfirmation') }}')) {
                 $('.form').submit();
            } else {
                return false;
            }
        }

    }
    /*------END----*/
</script>



@endsection