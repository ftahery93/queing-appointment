@extends('layouts.master')

@section('title')
Import-Export Data
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">


@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Import / Export Data
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">


            <div class="panel-body  table-responsive">
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-8">Import Data</th>
                            <th class="text-center col-sm-2">Created On</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>


                </table>
            </div>

        </div>

    </div>
</div>
<!-- Modal 1 (Ajax Modal)-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"  style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Imported Data List</h4>
            </div>
            <div class="modal-body">
                <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table border_0" id="importedfileslist">

                    <thead>
                        <tr>
                            <th class="col-sm-5"><b>Imported File Name</b></th>
                            <th class="col-sm-5 text-center"><b>Download Imported Data</b></th>
                            <th class="col-sm-2"><b>Created On</b></th>
                        </tr>                            
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
        ajax: '{{ url("admin/importexportdata") }}',
        language: {
            processing: "<img src='{{ asset('assets/images/loader-1.gif') }}'>"
        },
        columns: [
            {data: 0, name: 'table_name'},
            {data: 1, name: 'created_at', class: 'text-center'},
            {data: 3, name: 'action', orderable: false, searchable: false, class: 'text-center'}
        ],
        order: [[1, 'ASC']],
        "fnDrawCallback": function (oSettings) {

            /*----Status Update---*/
            $('.importedlist').on('click', function (e) {
                e.preventDefault();
                var ID = $(this).attr('data-val');
                $('.loading-image').show();
                $.ajax({
                    type: "GET",
                    async: true,
                    url: "{{ url('admin/importexportdata/imported_list')}}/"+ID,
                    success: function (data) {
                        $('#importedfileslist tbody').html(data.html);
                    },
                    complete: function () {
                        $('.loading-image').hide();
                    }
                });
            });
            /*------END----*/
        },
    });

});
</script>

@endsection