@extends('trainerLayouts.master')

@section('title')
Subscribers -Archived 
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/minimal/_all.css') }}">

@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Subscribers -Archived 
@endsection

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default" data-collapsed="0">


            <div class="col-sm-12 col-md-12 col-lg-12">

                <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                    <li class="{{ active('trainer/subscribers') }}">
                        <a href="{{ url('trainer/subscribers')  }}" >
                            <span class="visible-xs"><i class="entypo-home"></i></span>
                            <span class="hidden-xs">Active</span>
                        </a>
                    </li>
                    <li class="{{ active('trainer/archivedSubscribers') }}">
                        <a href="{{ url('trainer/archivedSubscribers')  }}" >
                            <span class="visible-xs"><i class="entypo-user"></i></span>
                            <span class="hidden-xs">Archived</span>
                        </a>
                    </li>

                </ul>

                <div class="tab-content">

<!--                    <div class="col-sm-12 margin10">
                        <div class="col-sm-6">
                            <label for="log" class="col-sm-2 control-label">Subscribers</label>

                            <div class="col-sm-9">
                                <select name="log" class="select2" data-allow-clear="true" id="log" onchange="GetSelectedTextValue()" >
                                    <option value="0">--Select Subscriber</option>
                                    @foreach ($Subscribers as $User)
                                    <option value="{{ $User->subscriber_id }}"> {{ $User->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>-->

                    <div class="tab-pane active" id="active">

                        <div class="panel-body  table-responsive">
                            <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                            <table class="table table-bordered datatable" id="table-4">
                                <thead>
                                    <tr>
                                        <th class="col-sm-3">Subscriber</th>
                                        <th class="col-sm-2">Email ID</th>
                                        <th class="text-center col-sm-1">Mobile</th>
                                        <th class="text-center col-sm-1">Area</th>
                                        <th class="text-center col-sm-1">Gender</th>
                                        <th class="text-center col-sm-1">No. of Classes</th>
                                        <th class="text-center col-sm-1">Attendance</th>
                                        <th class="text-center col-sm-2">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>

                </div>


            </div>


            <div class="clear visible-xs"></div>


        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
                                    jQuery(document).ready(function ($) {
                                        var $table4 = jQuery("#table-4");

                                        //Archived Classes                       
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
                                                "url": '{{ url("trainer/archivedSubscribers") }}',
                                                data: function (data) {
                                                    data.id = $('#log').val();
                                                },
                                                complete: function () {
                                                    $('.loading-image').hide();
                                                }
                                            },
                                            columns: [
                                                {data: 2, name: 'name'},
                                                {data: 3, name: 'email', orderable: false, searchable: false, class: 'text-center'},
                                                {data: 4, name: 'mobile', class: 'text-center'},
                                                {data: 5, name: 'area', class: 'text-center'},
                                                {data: 6, name: 'gender', orderable: false, searchable: false, class: 'text-center'},
                                                {data: 8, name: 'num_class', orderable: false, searchable: false, class: 'text-center'},
                                                {data: 9, name: 'attendance', orderable: false, searchable: false, class: 'text-center  text-success bold'},
                                                {data: 10, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                                            ],
                                            order: [[0, 'desc']],
                                            buttons: [
                                                //'copyHtml5',
                                                'excelHtml5',
                                                'csvHtml5',
                                                'pdfHtml5'
                                            ]
                                        });

                                    });

                                    function GetSelectedTextValue() {
                                        var $table4 = $("#table-4");
                                        $table4.DataTable().draw();
                                    }
</script>

@endsection