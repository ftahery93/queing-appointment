@extends('vendorLayouts.authmaster')

@section('title')
Log in
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
  
    .login-page .login-form .form-group .btn-login {
        height: 51px;
        padding: 0px 10px;
    }
      #vendor_index .login-form{transform: translate(0%, 50%);-moz-transform: translate(0%, 50%);}
</style>

@endsection

@section('content')

<div class="login-container" id="vendor_index">

    <div class="login-form">

        <div class="login-content">
                
            <form role="form"  autocomplete="off"  method="POST" action="{{ url(config('global.fitflowVendor').'/store') }}" id="form1">
                {{ method_field('POST') }}
                {{ csrf_field() }}
                 @if(count($errors))
                @include('vendorLayouts.flash-message')
                @yield('form-error')
                @endif                 
                @if(Session::has('error'))
                @include('vendorLayouts.flash-message')
                @endif
                

                <div class="row" id="container">
                    <div class="col-sm-12 block1">
                        <span><img src="{{ asset('assets/images/fitflow_logo_white.png') }}" width="100" alt="" /></span>  
                        <span class="font1">Log in</span>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">

                            <div class="input-group">                        
                                <input type="text" class="form-control" id="code"  autocomplete="off" value="{{ old('code') }}" name="code" placeholder="Your Code" required >
                                <div class="input-group-addon">
                                    {{ $_SERVER['SERVER_NAME'] }}
                                </div>     
                            </div>
                            <div id="error_code"></div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                          <div class="login_button">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-login">                                
                                NEXT 
                            </button>
                        </div>
                              </div>
                        
                    </div>

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
            code: "required",
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "code") {
                error.appendTo('#error_code');
                return;
            }
        }

    });

});

</script>
@endsection