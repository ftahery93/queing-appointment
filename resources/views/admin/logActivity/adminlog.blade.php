@extends('layouts.master')

@section('title')
Admin Users - Log Activities
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Admin Users - Log Activities
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
                                        "url": '{{ url("admin/logActivity") }}',
                                        data: function (data) {
                                            data.id = $('#log').val();
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
                                });
                                
                            });
                            
                            // On change trainer name
                            function GetSelectedTextValue() {
                                var $table4 = $("#table-4");
                                $table4.DataTable().draw();
                            }
                            </script>

@endsection