@extends('vendorLayouts.master')

@section('title')
Log Activities
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Log Activities
@endsection
 
    <div class="row">
        <div class="col-md-12">
            @include('vendorLayouts.flash-message')

            <div class="panel panel-dark" data-collapsed="0">
               <div class="row margin10">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <label for="field-2" class="col-sm-3 control-label">Date Range</label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control daterange" name="daterange" id="daterange" />
                                <input type="hidden" id="start_date" name="start_date"/>
                                <input type="hidden" id="end_date" name="end_date"/>
                            </div>
                        </div>
                        
                    </div>
                    </div>

                <div class="panel-body  table-responsive">
                    <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                    <table class="table table-bordered datatable" id="table-4">
                        <thead>
                            <tr>
                                <th class="col-sm-5">Subject</th>
                                <th class="col-sm-2">Ip</th>
                                 <th class="col-sm-3">URL</th>
                                <th class="col-sm-2">Created On</th>
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
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
                            jQuery(document).ready(function ($) {
                               $('#daterange, #start_date, #end_date').val(''); 
                                var $table4 = jQuery("#table-4");
                                $table4.DataTable({
                                    dom: 'lBfrtip',
                                    "stateSave": true,
                                     "pageLength": 50,
                                    processing: true,
                                    serverSide: true,
                                    ordering: true,
                                    language: {
                                        processing: "<img src='{{ asset('assets/images/loader-1.gif') }}'>"
                                    },
                                    "ajax": {
                                        "type": "GET",
                                        "url": '{{ url("$configName/logActivity") }}',
                                         data: function (data) {
                                            data.start_date = $('#start_date').val();
                                             data.end_date = $('#end_date').val();
                                        },
                                        complete: function () {
                                            $('.loading-image').hide();
                                        }
                                    },
                                    columns: [
                                        {data: 0, name: 'subject',orderable: false},
                                        {data: 1, name: 'ip', class: 'text-center',orderable: false},
                                        {data: 2, name: 'url',orderable: false},
                                        {data: 3, name: 'created_at', class: 'text-center'}
                                    ],
                                    order: [[3, 'desc']],
                                    buttons: [
                                        //'copyHtml5',
                                        'excel',
                                        'csvHtml5',
                                        'pdfHtml5',
                                        'print',
                                    ]
                                });
                                
                            });
                           
                                                        
</script>
<script>
    // On change trainer name
                            function GetSelectedTextValue() {
                                var $table4 = $("#table-4");
                                $table4.DataTable().draw();
                            }
$('#daterange').daterangepicker({
                autoUpdateInput : false,
                locale:{
                     format: 'DD/MM/YYYY',
                }
            }).on('apply.daterangepicker', function(ev, picker){
                $(this).val(picker.startDate.format('DD/MM/YYYY')+'  (To)  '+picker.endDate.format('DD/MM/YYYY'));
                $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));   
                 GetSelectedTextValue();
            }).on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#start_date').val('');
                $('#end_date').val('');
                 GetSelectedTextValue();
            });
</script> 
@endsection