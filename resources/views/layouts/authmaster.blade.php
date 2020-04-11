<!DOCTYPE html>
<html lang="en">
<head>

    @include(('layouts.meta'))

    <title>{{ $appTitle->title }} - Admin/@yield('title')</title>

    @include('layouts.css')

    @yield('css')
<script src="{{ asset('assets/js/jquery-1.11.3.min.js') }}"></script>

</head>

<body class="page-body login-page login-form-fall">
    @yield('content')
    
@yield('scripts')

</body>
</html>