@extends('vendorLayouts.master')

@section('title')
Upload Excelsheet
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<style>
    .loading-image{z-index:999;}
</style>
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Upload Excelsheet
@endsection

<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url($configM2.'/uploadSchedule') }}" id="form1"  enctype="multipart/form-data">
    {{ method_field('POST') }}
    {{ csrf_field() }}

    <div class="row">
        <div class="loading-image" style="display:none;"><img src='{{ asset('assets/images/loading_error.gif') }}' style="height:150px;">
            <p style="color:#252525;">Please Wait..</p>
        </div>
        <div class="col-md-12">
            @if(count($errors))
            @include('vendorLayouts.flash-message')
            @yield('form-error')
            @endif

            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="button" class="btn btn-green btn-icon" id="save">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url($configM2.'/uploadSchedule') }}" class="margin-top0">
                            <button type="button" class="btn btn-red btn-icon">
                                Cancel
                                <i class="entypo-cancel"></i>
                            </button>
                        </a>

                    </div>
                </div>

                <div class="panel-body">


                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('table_name') ? ' has-error' : '' }}">
                                <label for="table_name" class="col-sm-3 control-label">Title</label>
                                <div class="col-sm-9">
                                    <input type="hidden" value="{{ $import_tables->id }}" name="imported_table_id" id="imported_table_id">
                                    <input type="text" readonly="readonly" value="{{ ucfirst($import_tables->table_name) }}" name="table_name" class="form-control" id="table_name" >
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">                       
                        <div class="col-sm-12">                     
                            <img src="{{ asset('importdata_tables_images/'.$import_tables->image) }}" class="img-responsive"  />
                        </div>
                        <label for="table_name" class="col-sm-12 text-left" style="margin-top:15px;margin-bottom:20px;">Please follow above excel sheet to import data, Limit - <strong style="color:red;">10000 records</strong></label>
                    </div>

                    <div class="row">                       
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label for="table_name" class="col-sm-3 control-label">Upload</label>
                                <div class="col-sm-9">
                                    <input type="file" name="import_file" class="form-control file2 inline btn btn-primary" id="import_file" data-label="<i class='glyphicon glyphicon-file'></i> Browse" />
                                </div>
                            </div>
                            <!--                             <div class="col-sm-6">
                                                              <label for="table_name" class="col-sm-3 control-label">Export Data</label>
                                                              <div class="col-sm-9">
                                                                  <button class="btn btn-success">Download Data</button>
                                                              </div>
                                                         </div>-->
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            table_name: "required"
        },

    });

    /*----Excel Import file---*/
    $('#save').on('click', function (e) {
        var data = new FormData();

          //Form data
        var form_data = $('#form1').serializeArray();
        $.each(form_data, function (key, input) {
            data.append(input.name, input.value);
        });

         //File data
        //var file_data = $('input[name="import_file"]')[0].files;
         var file_data = $('#import_file').prop('files')[0];
        
       // for (var i = 0; i < file_data.length; i++) {
            data.append("import_file", file_data);
        //}
       
//        var file_data = $('#import_file').prop('files')[0];
//        var form_data = new FormData();
//        form_data.append('file', file_data);
//        var imported_table_id = $('#imported_table_id').val();
//        var import_file = $('#import_file').val();
        var table_name = $('#table_name').val();
        $('.loading-image').show();
        $.ajax({
            type: "POST",
             data: data,
            contentType: false,
            cache: false,
            processData: false,
            "url": '{{ url("$configM2/uploadSchedule") }}',
            //data: {imported_table_id: imported_table_id, import_file: import_file, table_name: table_name, _token: '{{ csrf_token() }}'},
            success: function (data) {
                if (data.error) {
                    toastr.error(data.error, "", opts2);
                }
                if (data.success) {
                   window.location.href='{{ url("$configM2/schedules") }}';
                }
            },
            complete: function () {
                $('.loading-image').hide();
            }
        });
    });
    /*------END----*/

    // Sample Toastr Notification
    var opts2 = {
        "closeButton": true,
        "debug": false,
        "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
        "toastClass": "error",
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "8000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
});

</script>
@endsection