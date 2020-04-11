@extends('layouts.master')

@section('title')
Trainer {{ ucfirst($trainerName->name) }} - Packages
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">

@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/trainers')  }}">Trainers</a>
</li>
@endsection

@section('pageheading')
Trainer {{ ucfirst($trainerName->name) }} - Packages
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
                            <th class="col-sm-2">Package Name</th>
                            <th class="col-sm-2">No. Classes</th>
                            <th class="col-sm-2">No. of Days</th>
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
                                        "url": '{{ url("admin/trainers/$trainer_id/packages") }}',
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'name_en'},
                                        {data: 1, name: 'num_points', class: 'text-center'},
                                        {data: 2, name: 'num_days', class: 'text-center'},
                                        {data: 3, name: 'price', class: 'text-center'},
                                        {data: 4, name: 'created_at', class: 'text-center'}
                                    ],
                                    order: [[4, 'desc']],
                                    buttons: [
                                        //'copyHtml5',
                                        'excelHtml5',
                                        'csvHtml5',
                                        'pdfHtml5'
                                    ]
                                });
                            });

                           // On change trainer name
                            function GetSelectedTextValue() {
                                var $table4 = $("#table-4");
                                $table4.DataTable().draw();
                            }
</script>

@endsection