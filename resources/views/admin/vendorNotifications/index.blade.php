@extends('layouts.master')

@section('title')
Vendor Notifications
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

@endsection

@section('pageheading')
Vendor Notifications
@endsection
<form class="form" role="form" method="POST" action="{{ url('admin/vendorNotifications/delete')  }}" >  
    {{ csrf_field() }} 
    <div class="row">
        <div class="col-md-12">
            @include('layouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">


                <div class="panel-heading">

                    <div class="panel-options">
                        @if ($CreateAccess==1)
                        <a href="{{ url('admin/vendorNotifications/create')  }}" class="margin-top0">
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
                        @endif

                    </div>
                </div>

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="text-center" id="td_checkbox"><input tabindex="5" type="checkbox" class="icheck-14"  id="check-all"></th>
                                <th class="col-sm-1">Sent To</th>
                                <th class="col-sm-3">Subject</th>
                                <th class="col-sm-2">DateTime</th>
                               <th class="col-sm-2">Sent / Received</th>
                                <th class="text-center col-sm-1">Actions</th>
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
                                       "url": '{{ url("admin/vendorNotifications") }}',
                                       complete: function () {
                                           $('.loading-image').hide();
                                       }
                                   },
                                   columns: [
                                       {data: 0, name: 'id', orderable: false, searchable: false, class: 'text-center checkbox_padding'},
                                       {data: 1, name: 'send_to', class: 'text-center'},
                                       {data: 2, name: 'subject', orderable: false},
                                       {data: 3, name: 'notification_date', class: 'text-center'},
                                       {data: 4, name: 'num_sent', class: 'text-center', orderable: false, searchable: false},
                                       {data: 5, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                                   ],
                                   order: [[3, 'desc']],
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

                                   },
                                   buttons: [
                                       //'copyHtml5',
                                       'excelHtml5',
                                       'csvHtml5',
                                       'pdfHtml5'
                                   ]
                               });
                           });
</script>
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