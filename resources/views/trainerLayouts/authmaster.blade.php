<!DOCTYPE html>
<html lang="en">
<head>

    @include(('trainerLayouts.meta'))

    <title>Queue/@yield('title')</title>

    @include('trainerLayouts.css')

    @yield('css')
<script src="{{ asset('assets/js/jquery-1.11.3.min.js') }}"></script>

</head>

<body class="page-body login-page login-form-fall">
    @yield('content')
    
@yield('scripts')

</body>
</html>