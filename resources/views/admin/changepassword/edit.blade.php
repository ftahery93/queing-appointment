@extends('layouts.master')

@section('title')
Change Password
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Change Password
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/admin/user/changepassword')  }}" method="POST" id="form1">
    <input type="hidden" name="_method" value="PUT">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('layouts.flash-message')
            @yield('form-error')
            @endif
            
            @if(Session::has('error'))
            @include('layouts.flash-message')
            @endif
            
             @if(Session::has('message'))
            @include('layouts.flash-message')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url("admin/dashboard") }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('current_password') ? ' has-error' : '' }}">

                                <label for="current_password" class="col-sm-5 control-label">Current Password</label>

                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="current_password" autocomplete="off"  name="current_password">
                                    @if ($errors->has('current_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('current_password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>



                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('new_password') ? ' has-error' : '' }}">
                                <label for="new_password" class="col-sm-5 control-label">New Password</label>

                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="new_password" autocomplete="off"  name="new_password"> 
                                    @if ($errors->has('new_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('new_password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('new_password_confirmation') ? ' has-error' : '' }}">
                                <label for="new_password_confirmation" class="col-sm-5 control-label">Confirm New Password</label>

                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="new_password_confirmation" autocomplete="off"  name="new_password_confirmation"> 
                                    @if ($errors->has('new_password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('new_password_confirmation') }}</strong>
                                    </span>
                                    @endif
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

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            current_password: "required",
            new_password: {
                required: true,
                minlength: 5
            },
            new_password_confirmation: {
                required: true,
                minlength: 5,
                equalTo: "#new_password"
            }
        },

    });

});

</script>
@endsection