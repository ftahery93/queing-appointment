@extends('trainerLayouts.authmaster')

@section('title')
Login
@endsection

@section('css')
<style>
    body{background-image: url('{{ asset("assets/images/fitflow_admin_Login_bg.jpg") }}') !important;background-size: cover !important;background-position: center center !important;}
    .login-page .login-header{background:none !important;}
    .login-page .login-form .form-group .input-group{background-color:#fff !important;}
    .login-page .login-form .form-group .input-group .form-control {
        color:#252525 !important;
    }
    .login-page .login-form .form-group .btn-login{background-color:#000 !important;}
    .login-page .login-form{padding-top:38px;}
    .login-page .login-form .form-group .btn-login {
        height: 51px;
        padding: 0px 10px;
    }
</style>

@endsection

@section('content')

<div class="login-container">

    <div class="login-header">

        <div class="login-content">

            <a href="{{ url('trainer') }}" class="logo">
                <img src="{{ asset('assets/images/fitflow_logo_white.png') }}" width="120" alt="" />
            </a> 


        </div>

    </div>

    <div class="login-progressbar">
        <div></div>
    </div>

    <div class="login-form">

        <div class="login-content">

            <form role="form"  autocomplete="off"  method="POST" action="{{ url('/trainer/login') }}" id="form1">
                {{ method_field('POST') }}
                {{ csrf_field() }}

                @if(count($errors))
                @include('trainerLayouts.flash-message')
                @yield('form-error')
                @endif                 
                @if(Session::has('error'))
                @include('trainerLayouts.flash-message')
                @endif
                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-user"></i>
                        </div>
                        <input type="text" class="form-control" id="username"  autocomplete="off" value="{{ old('username') }}" name="username" placeholder="Username" required >

                        <!--                        @if ($errors->has('username'))
                        
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('username') }}</strong>
                                                </span>
                                                @endif-->
                    </div>
                    <div id="error_username"></div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-key"></i>
                        </div>

                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off" required/>

                        <!--                        @if ($errors->has('password'))
                        
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                                @endif-->

                    </div>
                    <div id="error_password"></div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block btn-login">
                        <i class="entypo-login"></i>
                        Log In
                    </button>
                </div>


            </form>


            <div class="login-bottom-links">

                <a href="{{ url('/trainer/password/reset') }}" class="link">Forgot your password?</a>

                <br />

                <!-- <a href="#">ToS</a>  - <a href="#">Privacy Policy</a> -->

            </div>

        </div>

    </div>

</div>
@endsection

@section('scripts')
<!-- Bottom scripts (common) -->
<script src="{{ asset('assets/js/gsap/TweenMax.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/js/joinable.js') }}"></script>
<script src="{{ asset('assets/js/resizeable.js') }}"></script>
<script src="{{ asset('assets/js/neon-api.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/neon-login.js') }}"></script>


<!-- JavaScripts initializations and stuff -->
<script src="{{ asset('assets/js/neon-custom.js') }}"></script>


<!-- Demo Settings -->
<script src="{{ asset('assets/js/neon-demo.js') }}"></script>
<script src="{{ asset('assets/js/neon-login.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            username: "required",
            password: {
                required: true,
                minlength: 6
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "username") {
                error.appendTo('#error_username');
                return;
            }

            if (element.attr("name") == "password") {
                error.appendTo('#error_password');
                return;
            }
        }

    });

});

</script>
@endsection