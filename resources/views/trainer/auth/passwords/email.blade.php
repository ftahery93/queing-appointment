@extends('trainerLayouts.authmaster')

@section('title')
Reset Password
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


<!-- Main Content -->
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

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/trainer/password/email') }}" id="form1">
                @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
                 @endif

                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-mail"></i>
                        </div>
                        <input type="email" class="form-control" id="email"  autocomplete="off" value="{{ old('email') }}" name="email" placeholder="E-Mail Address" required autocomplete="off">
                                               
                    </div>
                    <div id="error_email"></div>
                    @if ($errors->has('email'))
                        
                                               <div id="error_email">
                                                    <strong class="help-block">{{ $errors->first('email') }}</strong>
                                                </div>
                                                @endif
                    
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block btn-login">
                        <i class="entypo-login"></i>
                        Send Password Reset Link
                    </button>
                </div>

            </form>



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
            email: "required",

        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "email") {
                error.appendTo('#error_email');
                return;
            }


        }

    });

});

</script>
@endsection