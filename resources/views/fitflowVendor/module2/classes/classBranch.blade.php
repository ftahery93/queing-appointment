@extends('vendorLayouts.master')

@section('title')
Branch wise Classes
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">

@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Branch wise Classes
@endsection

    <div class="row">
        <div class="col-md-12">
            @include('vendorLayouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">
            <div class="row margin10">
                <div class="col-sm-12">
                   
                    <div class="col-sm-4">
                        <label for="class_master_id" class="col-sm-4 control-label">Classes</label>

                        <div class="col-sm-8">
                            <select name="class_master_id" class="select2" data-allow-clear="true" id="class_master_id" onchange="GetSelectedTextValue()" >
                                <option value="0">--All--</option>
                                @foreach ($ClassMasters as $ClassMaster)
                                <option value="{{ $ClassMaster->id }}"> {{ $ClassMaster->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                  
                </div>
            </div>
                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                 <th class="col-sm-3">Class Name</th>
                                <th class="col-sm-3">Branch</th>
                                <th class="text-center col-sm-1">Total Seats</th> 
                                <th class="text-center col-sm-1">Gym Seats</th>                                                               
                                <th class="text-center col-sm-2">{{ $appTitle->title }} Seats</th>
                                <th class="text-center col-sm-1">Created On</th>
                                <th class="text-center col-sm-1">Rating</th>
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
                                            "url":  '{{ url("$configM2/classBranch") }}',
                                            data: function (data) {
                                                data.class_master_id = $('#class_master_id').val();
                                                },
                                            complete: function () {
                                            $('.loading-image').hide();
                                            }
                                    },
                                    columns: [
                                    {data: 0, name: 'classname', orderable: false, searchable: false},
                                    {data: 1, name: 'branch', orderable: false},
                                    {data: 2, name: 'num_seats', orderable: false, class: 'text-center'},
                                    {data: 3, name: 'available_seats', orderable: false, class: 'text-center'},
                                    {data: 4, name: 'fitflow_seats', orderable: false, class: 'text-center'},
                                    {data: 5, name: 'created_at', class: 'text-center'},
                                    {data: 6, name: 'rating', class: 'text-center'},
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
                                                        
// On change ID
    function GetSelectedTextValue() {
        var $table4 = $("#table-4");
        $table4.DataTable().draw();
    }
</script>

@endsection