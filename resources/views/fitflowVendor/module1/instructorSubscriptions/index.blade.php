@extends('vendorLayouts.master')

@section('title')
Instructor Subscriptions
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">

@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Instructor Subscriptions
@endsection
    <div class="row">
        <div class="col-md-12">
            @include('vendorLayouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">


                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="col-sm-9">Package Name</th>
                                <th class="col-sm-2 text-right">Price</th>
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
                                        "url": '{{ url("$configM1/instructorSubscriptions") }}',
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [                                      
                                        {data: 0, name: 'name_en'},
                                        {data: 1, name: 'price', orderable: false, searchable: false, class: 'text-right'},
                                        {data: 3, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                                    ],
                                    order: [[2, 'desc']],                                   
                                    buttons: [
                                        //'copyHtml5',
                                        'excelHtml5',
                                        'csvHtml5',
                                        'pdfHtml5'
                                    ]
                                });

                            });
</script>
@endsection