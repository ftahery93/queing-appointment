@extends('layouts.master')

@section('title')
Contact us
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Contact us
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">


            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-2">Full Name</th>
                            <th class="col-sm-2">Email ID</th>
                            <th class="col-sm-2">Mobile</th>
                            <th class="col-sm-3">Message</th>
                            <th class="col-sm-2">Date</th>
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
            "url": '{{ url("admin/contactus") }}',
            complete: function () {
                $('.loading-image').hide();
            }
        },
        columns: [
            {data: 0, name: 'fullname', orderable: false},
            {data: 1, name: 'email', class: 'text-center', orderable: false},
            {data: 2, name: 'mobile', class: 'text-center', orderable: false},
            {data: 3, name: 'message', orderable: false, },
            {data: 4, name: 'created_at', class: 'text-center'}
        ],
        order: [[4, 'desc']],
    });

});</script>

@endsection