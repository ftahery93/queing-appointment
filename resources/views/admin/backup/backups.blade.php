@extends('layouts.master')

@section('title')
Database Backups
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Database Backups
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">


            <div class="panel-heading">

                <div class="panel-options">
                    <a href="{{ url('admin/backup/create')  }}" class="margin-top0">
                        <button type="button" class="btn btn-default btn-icon">
                            Create New Backup
                            <i class="entypo-plus padding10"></i>
                        </button>
                    </a>

                </div>
            </div>

            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-5">File</th>
                            <th class="col-sm-2">Size (bytes)</th>
                            <th class="col-sm-2">Created On</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>

    </div>
</div>

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
        ordering: true,
        language: {
            processing: "<img src='{{ asset('assets/images/loader-1.gif') }}'>"
        },
        "ajax": {
            "type": "GET",
            "url": '{{ url("admin/backup") }}',
            complete: function () {
                $('.loading-image').hide();
            }
        },
        columns: [
            {data: 1, name: 'file_name'},
            {data: 2, name: 'file_size'},
            {data: 3, name: 'created_at', class: 'text-center'},
            {data: 4, name: 'action', orderable: false, searchable: false, class: 'text-center'}
        ],
        order: [[0, 'desc']],
        "fnDrawCallback": function (oSettings) {
            /*----Delete Record---*/
            $('.delete').on('click', function (e) {
                e.preventDefault();
                //Confirm before delete
                if (confirm("Are you sure?")) {
                    var ID = $(this).attr('data-id');
                    $.ajax({
                        type: "POST",
                        async: true,
                        url: "{{ url('admin/backup')}}/"+ID + '/delete',
                        data: {id: ID, _token: '{{ csrf_token() }}'},
                        success: function (data) {
                            $table4.DataTable().ajax.reload(null, false);
                            toastr.success(data.response, "", opts);
                        }
                    });
                } else {
                    return false;
                }

            });
            /*------END----*/
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
});</script>
            <script type="text/javascript">
                /*---On Delete All Confirmation---*/
                function ConfirmDelete() {
                    if (confirm("Are you sure?")) {
                        $('.form').submit();
                    } else {
                        return false;
                    }
                }
                /*------END----*/
            </script>

            @endsection