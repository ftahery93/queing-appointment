@extends('layouts.master')

@section('title')
 Expired Contracts
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
Expired Contracts
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">

            <div class="col-sm-12 margin10">
                <div class="col-sm-6">
                    <label for="Vendor_id" class="col-sm-2 control-label">Type</label>

                    <div class="col-sm-9">
                        <select name="type" class="select2" data-allow-clear="true" id="type" onchange="GetSelectedTextValue()" >
                            <option value="0">--Select Type</option>
                            <option value="1"> Vendors</option>
                            <option value="2"> Trainers</option>
                        </select>
                    </div>
                </div>
                
            </div>
            <div class="panel-body  table-responsive">
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-3">Name</th>
                            <th class="col-sm-3">Contract</th>
                            <th class="col-sm-2">Start Date</th>
                            <th class="col-sm-2">End Date</th>
                            <th class="text-center col-sm-1">Actions</th>
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
                                        "url": '{{ url("admin/expiredContracts") }}',
                                        data: function (data) {
                                            data.type = $('#type').val();
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'name'},
                                        {data: 1, name: 'contract_name'},
                                        {data: 2, name: 'contract_startdate', class: 'text-center'},
                                        {data: 3, name: 'contract_enddate', class: 'text-center'},
                                        {data: 5, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                                    ],
                                    order: [[3, 'desc']],
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