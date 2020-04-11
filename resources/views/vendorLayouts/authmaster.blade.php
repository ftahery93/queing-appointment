<!DOCTYPE html>
<html lang="en">
<head>

    @include(('vendorLayouts.meta'))

    <title>{{ $appTitle->title }} - Vendor/@yield('title')</title>

    @include('vendorLayouts.css')

    @yield('css')
<script src="{{ asset('assets/js/jquery-1.11.3.min.js') }}"></script>

</head>

<body class="page-body login-page login-form-fall">
    @yield('content')
    
@yield('scripts')

</body>
</html>