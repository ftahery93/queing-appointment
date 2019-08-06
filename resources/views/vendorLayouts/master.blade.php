<!DOCTYPE html>
<html lang="en">
<head>

    @include(('vendorLayouts.meta'))

    <title>{{ $appTitle->title }} - Vendor/@yield('title')</title>

    @include('vendorLayouts.css')

    @yield('css')

</head>

<body class="page-body page-fade-only">

<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->


@include('vendorLayouts.nav')

<div class="main-content">
@include('vendorLayouts.header')

    @yield('content')


</div> <!-- /container -->

@include('vendorLayouts.scripts')



@yield('scripts')

    @include('vendorLayouts.bottom')
