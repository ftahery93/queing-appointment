@extends('layouts.master')

@section('title')
Instructor Packages
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">

@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Instructor Packages
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">

            <div class="col-sm-12 margin10">
                <div class="col-sm-6">
                    <label for="Vendor_id" class="col-sm-2 control-label">Vendors</label>

                    <div class="col-sm-9">
                        <select name="vendor_id" class="select2" data-allow-clear="true" id="vendor_id" onchange="GetSelectedTextValue()" >
                            <option value="0">--Select Vendor</option>
                            @foreach ($Vendors as $Vendor)
                            <option value="{{ $Vendor->id }}"> {{ $Vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-2">Vendor Name</th>
                             <th class="col-sm-2">Branch Name</th>
                            <th class="col-sm-2">Package Name</th>
                            <th class="col-sm-2">No. Sessions</th>
                            <th class="col-sm-2">Price</th>
                            <th class="text-center col-sm-2">Created On</th>
                        </tr>
                    </thead>


                </table>
            </div>

        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
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
                                        "url": '{{ url("admin/instructorPackages") }}',
                                        data: function (data) {
                                            data.id = $('#vendor_id').val();
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'name'},
                                        {data: 1, name: 'name_en'},
                                         {data: 2, name: 'branch_name'},
                                        {data: 3, name: 'num_points', class: 'text-center'},
                                        {data: 4, name: 'price', class: 'text-center'},
                                        {data: 5, name: 'created_at', class: 'text-center'}
                                    ],
                                    order: [[5, 'desc']],
                                    buttons: [
                                        //'copyHtml5',
                                        'excelHtml5',
                                        'csvHtml5',
                                        'pdfHtml5'
                                    ]
                                });
                            });

                           // On change Vendor name
                            function GetSelectedTextValue() {
                                var $table4 = $("#table-4");
                                $table4.DataTable().draw();
                            }
</script>

@endsection