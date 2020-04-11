@extends('layouts.master')

@section('title')
Import Data
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Import Data
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('/trainer/importexportdata') }}" id="form1"  enctype="multipart/form-data">
    {{ method_field('POST') }}
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
           @if(count($errors))
            @include('layouts.flash-message')
            @yield('form-error')
            @endif
            
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url('trainer/importexportdata') }}" class="margin-top0">
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
                                <label for="table_name" class="col-sm-3 control-label">Import Data</label>
                                <div class="col-sm-9">
                                    <input type="hidden" value="{{ $import_tables->id }}" name="imported_table_id">
                                    <input type="text" readonly="readonly" value="{{ ucfirst($import_tables->table_name) }}" name="table_name" class="form-control" >
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
                                  <label for="table_name" class="col-sm-3 control-label">Import Data</label>
                                  <div class="col-sm-9">
                                      <input type="file" name="import_file" class="form-control file2 inline btn btn-primary" data-label="<i class='glyphicon glyphicon-file'></i> Browse" />
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
<script type="text/javascript">
$(document).ready(function () {

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            table_name: "required"
        },

    });

});

</script>
@endsection