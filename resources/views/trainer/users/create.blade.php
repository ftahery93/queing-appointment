@extends('trainerLayouts.master')

@section('title')
Workers
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('trainer/users') }}">Users</a>
</li>
@endsection

@section('pageheading')
Workers
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('trainer/users') }}" id="form1">
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
                        <a href="{{ url('trainer/users') }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-sm-3 control-label">Name</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" autocomplete="off" value="{{ old('name') }}" name="name">
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-sm-3 control-label">Email ID</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" autocomplete="off" value="{{ old('email') }}" name="email"> 
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>

                   

                    <div class="row">
                        <div class="form-group col-sm-12">                          

                            <div class="col-sm-6{{ $errors->has('branch_id') ? ' has-error' : '' }}">
                                <label for="branch_id" class="col-sm-3 control-label">Branch</label>
                                <div class="col-sm-9">
                                    <select name="branch_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select Branch</option>
                                        @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ (collect(old('branch_id'))->contains($branch->id)) ? 'selected':'' }} >{{ $branch->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('branch_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('branch_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                    
                    <?php /* ?>
                      <div class="row">
                      <div class="form-group col-sm-12">

                      <div class="col-sm-6{{ $errors->has('civilid') ? ' has-error' : '' }}">
                      <label for="civilid" class="col-sm-3 control-label">Civil ID</label>

                      <div class="col-sm-9">
                      <input type="tel" class="form-control" id="civilid" autocomplete="off"  value="{{ old('civilid') }}" name="civilid">
                      @if ($errors->has('civilid'))
                      <span class="help-block">
                      <strong>{{ $errors->first('civilid') }}</strong>
                      </span>
                      @endif
                      </div>
                      </div>

                      <div class="col-sm-6{{ $errors->has('user_role_id') ? ' has-error' : '' }}">
                      <label for="user_role_id" class="col-sm-3 control-label">User Role</label>

                      <div class="col-sm-9">
                      <select name="user_role_id" class="select2" data-allow-clear="true" >
                      <option value="">--Select--</option>
                      @foreach ($userroles as $userrole)
                      <option value="{{ $userrole->id }}" {{ (collect(old('user_role_id'))->contains($userrole->id)) ? 'selected':'' }} >{{ $userrole->name }}</option>
                      @endforeach
                      </select>
                      @if ($errors->has('user_role_id'))
                      <span class="help-block">
                      <strong>{{ $errors->first('user_role_id') }}</strong>
                      </span>
                      @endif
                      </div>
                      </div>


                      </div>

                      </div>
                      <?php */ ?>
                
                </div>

            </div>

        </div>
    </div>

</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    jQuery.validator.addMethod("noSpace", function (value, element) {
        return value.indexOf(" ") < 0 && value != "";
    }, "{{ config('global.spaceValidation') }}");

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {            
            name: "required",
            email: "required",
           
            branch_id: "required",
        },

    });

});

</script>
@endsection