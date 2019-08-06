@extends('vendorLayouts.master')

@section('title')
Subscribers
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/minimal/_all.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Subscribers 
@endsection

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default" data-collapsed="0">

            <div class="col-sm-12 col-md-12 col-lg-12">

                <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                    <li class="active">
                        <a href="{{ url($configM1.'/subscribers')  }}" >
                            <span class="visible-xs"><i class="entypo-home"></i></span>
                            <span class="hidden-xs">Active</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url($configM1.'/archivedSubscribers')  }}" >
                            <span class="visible-xs"><i class="entypo-user"></i></span>
                            <span class="hidden-xs">Archived</span>
                        </a>
                    </li>

                </ul>

                <div class="tab-content">

                    <div class="col-sm-12 margin10">

                        <div class="col-sm-6">
                            <label for="field-2" class="col-sm-4 control-label">Package End Date</label>

                            <div class="col-sm-7">
                                <input type="text" class="form-control daterange" name="daterange" id="daterange" placeholder="Filter Records" />
                                <input type="hidden" id="start_date" name="start_date"/>
                                <input type="hidden" id="end_date" name="end_date"/>
                            </div>
                        </div>

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
                    </div>

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


<!-- Modal 2 (Current Pacakge)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"   style="width: 25%;">
        <div class="modal-content"  id="current_package">


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/datatables.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
                                    jQuery(document).ready(function ($) {
                                        $('#daterange, #start_date, #end_date').val('');
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
                                                "url": '{{ url("$configM1/subscribers") }}',
                                                data: function (data) {
                                                    data.id = $('#log').val();
                                                    data.start_date = $('#start_date').val();
                                                    data.end_date = $('#end_date').val();
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
                                                {data: 7, name: 'action', orderable: false, searchable: false, class: 'text-center'}
                                            ],
                                            order: [[0, 'desc']],
                                            "fnDrawCallback": function (oSettings) {

                                                /*----Current Package---*/
                                                $('.current_package').on('click', function (e) {
                                                    e.preventDefault();
                                                    $('#current_package').html('');
                                                    var ID = $(this).attr('data-val');
                                                    $('.loading-image').show();
                                                    $.ajax({
                                                        type: "GET",
                                                        async: true,
                                                        "url": '{{ url("$configM1/subscribers")}}/' + ID + '/currentPackage',
                                                        success: function (data) {
                                                            $('#current_package').html(data.html);
                                                        },
                                                        complete: function () {
                                                            $('.loading-image').hide();
                                                        }
                                                    });
                                                });
                                                /*------END----*/

                                                /*----Current Pacakge Payment Details---*/
                                                $('.payment_details').on('click', function (e) {
                                                    e.preventDefault();
                                                    $('#current_package').html('');
                                                    var ID = $(this).attr('data-val');
                                                    $('.loading-image').show();
                                                    $.ajax({
                                                        type: "GET",
                                                        async: true,
                                                        "url": '{{ url("$configM1/subscribers")}}/' + ID + '/paymentDetails',
                                                        success: function (data) {
                                                            $('#current_package').html(data.html);
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


                                    });
                                    // On change trainer name
                                    function GetSelectedTextValue() {
                                        var $table4 = $("#table-4");
                                        $table4.DataTable().draw();
                                    }
</script>
<script>
    $('#daterange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'DD/MM/YYYY',
        }
    }).on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + '  (To)  ' + picker.endDate.format('DD/MM/YYYY'));
        $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
        $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
        GetSelectedTextValue();
    }).on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#start_date').val('');
        $('#end_date').val('');
        GetSelectedTextValue();
    });
</script> 
@endsection