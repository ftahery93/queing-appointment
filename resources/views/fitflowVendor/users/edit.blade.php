@extends('vendorLayouts.master')

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

    <a href="{{ url($configName.'/users') }}">Users</a>
</li>
@endsection

@section('pageheading')
Users
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url($configName.'/users/'. $User->id)  }}" method="POST" id="form1">
    <input type="hidden" name="_method" value="patch">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('vendorLayouts.flash-message')
            @yield('form-error')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url($configName.'/users') }}" class="margin-top0">
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
                                    <input type="text" class="form-control" id="name" autocomplete="off" value="{{ $User->name }}" name="name">
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
                                    <input type="email" class="form-control" id="email" autocomplete="off" value="{{ $User->email }}" name="email"> 
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
                                    <input type="tel" class="form-control number_only" id="mobile" autocomplete="off" value="{{ $User->mobile }}" name="mobile">
                                    @if ($errors->has('mobile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('permission_id') ? ' has-error' : '' }}">
                                <label for="permission_id" class="col-sm-3 control-label">Permission</label>
                                <div class="col-sm-9">
                                    <select name="permission_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select Permission</option>
                                        @foreach ($permissions as $permission)
                                        <option value="{{ $permission->id }}" @if($User->permission_id == $permission->id) selected  @endif >{{ $permission->groupname }}</option>
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
                                    <input type="tel" class="form-control" id="civilid" autocomplete="off"  value="{{ $User->civilid }}" name="civilid">
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
                                        <option value="{{ $userrole->id }}" @if($User->user_role_id == $userrole->id) selected  @endif >{{ $userrole->name }}</option>
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
                                    <input type="text" class="form-control" id="username" autocomplete="off"  value="{{ $User->username }}" name="username">
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
                                        <option value="1" @if($User->status == 1) selected  @endif> Active</option>
                                        <option value="0" @if($User->status == 0) selected  @endif> Deactive</option>
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
            permission_id: "required",
            mobile: {
                required: true,
                number: true,
               // minlength: 8,
                maxlength: 8
            },
        },

    });

});
$('.number_only').keypress(function (e) {
                            return isNumbers(e, this);
                            });
                            function isNumbers(evt, element)
                            {
                            var charCode = (evt.which) ? evt.which : event.keyCode;
                            if (
                                    (charCode != 46 || $(element).val().indexOf('.') != - 1) && // “.�? CHECK DOT, AND ONLY ONE.
                                    (charCode > 57))
                                    return false;
                            return true;
                            }
</script>
@endsection