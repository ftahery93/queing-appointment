@extends('layouts.master')

@section('title')
Package History
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

    <a href="{{ url("admin/vendors")  }}">Vendors</a>
</li>
<li>

    <a href="{{ url("admin/$vendor_id/members")  }}">Members</a>
</li>

@endsection

@section('pageheading')
Package History - {{ ucfirst(trans($username->name)) }}  <button type="button" class="btn btn-info btn-xs pull-right" style="font-size:15px;">{{ $Amount }} {{ config('global.amountCurrency') }}</button>
@endsection

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-dark" data-collapsed="0">                    

            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-2">Package Name</th>
                            <th class="col-sm-2 text-center">Price (KD</th>
                            <th class="col-sm-2 text-center">Start Date</th>
                            <th class="col-sm-2 text-center">End Date</th>  
                            <th class="col-sm-1 text-center">No. of Days</th>
                             <th class="col-sm-1 text-center">Payment Method</th>
                            <th class="text-center col-sm-1">Action</th>
                        </tr>
                    </thead>

                </table>
            </div>

            <div class="clear visible-xs"></div>


        </div>

    </div>
</div>
<!-- Modal 2 (Payment Details)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"   style="width: 25%;">
        <div class="modal-content"  id="paymentDetails">


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
            "url": '{{ url("admin/members/$vendor_id")}}/'+ID + '/packageHistory',
            complete: function () {
                $('.loading-image').hide();
            }
        },
        columns: [
            {data: 0, name: 'name_en'},
            {data: 1, name: 'price', class: 'text-center'},
            {data: 2, name: 'start_date', class: 'text-center'},
            {data: 3, name: 'end_date', class: 'text-center'},
            {data: 4, name: 'num_days', class: 'text-center'},
            {data: 5, name: 'payment_method', class: 'text-center'},
            {data: 9, name: 'action', class: 'text-center'}
        ],
        order: [[3, 'desc']],
           "fnDrawCallback": function (oSettings) {
                                /*----Payment Details---*/
                                $('.package_details').on('click', function (e) {
                                    e.preventDefault();
                                    $('#paymentDetails').html('');
                                    var payment_id = $(this).attr('data-val');
                                    $('.loading-image').show();
                                    $.ajax({
                                        type: "GET",
                                        async: true,
                                        "url": '{{ url("admin/members")}}/'+payment_id + '/packagePayment',
                                        success: function (data) {
                                            $('#paymentDetails').html(data.html);
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
});</script>

@endsection