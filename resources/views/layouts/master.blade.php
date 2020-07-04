<!DOCTYPE html>
<html lang="en">
<head>

    @include(('layouts.meta'))

    <title>Admin/@yield('title')</title>

    @include('layouts.css')

    @yield('css')

</head>

<body class="page-body page-fade-only">

<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->


@include('layouts.nav')

<div class="main-content">
@include('layouts.header')

    @yield('content')


</div> <!-- /container -->

@include('layouts.scripts')



@yield('scripts')

    @include('layouts.bottom')
