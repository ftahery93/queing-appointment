@extends('layouts.master')

@section('title')
Users
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/users') }}">Users</a>
</li>
@endsection

@section('pageheading')
Users
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url('admin/users') }}" id="form1">
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
                        <a href="{{ url('admin/users') }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('password') ? ' has-error' : '' }}">

                                <label for="password" class="col-sm-3 control-label">Password</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password" autocomplete="off"  name="password">
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation" class="col-sm-3 control-label">Confirm Password</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password_confirmation" autocomplete="off"  name="password_confirmation"> 
                                    @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                <label for="mobile" class="col-sm-3 control-label">Mobile</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control" id="mobile" autocomplete="off" value="{{ old('mobile') }}" name="mobile">
                                    @if ($errors->has('mobile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('permission_id') ? ' has-error' : '' }}">
                                <label for="permission_id" class="col-sm-3 control-label">Users Group</label>
                                <div class="col-sm-9">
                                    <select name="permission_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select Permission</option>
                                        @foreach ($permissions as $permission)
                                        <option value="{{ $permission->id }}" {{ (collect(old('permission_id'))->contains($permission->id)) ? 'selected':'' }} >{{ $permission->groupname }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('permission_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('permission_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                    <input type="hidden" value="5" name="user_role_id">
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
                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('username') ? ' has-error' : '' }}">
                                <label for="username" class="col-sm-3 control-label">Username</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="username" autocomplete="off"  value="{{ old('username') }}" name="username">
                                    @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="status" class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                        <option value="1" {{ (collect(old('status'))->contains(1)) ? 'selected':'' }}> Active</option>
                                        <option value="0" {{ (collect(old('status'))->contains(0)) ? 'selected':'' }}> Deactive</option>
                                    </select>
                                </div>
                            </div>


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
<script type="text/javascript">
$(document).ready(function () {
    jQuery.validator.addMethod("noSpace", function (value, element) {
        return value.indexOf(" ") < 0 && value != "";
    }, "{{ config('global.spaceValidation') }}");

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            username: {
                noSpace: true
            },
            name: "required",
            email: "required",
            user_role_id: "required",
            password: {
                required: true,
                minlength: 6
            },
            password_confirmation: {
                required: true,
                minlength: 6,
                equalTo: "#password"
            },
            permission_id: "required",
        },

    });

});

</script>
@endsection