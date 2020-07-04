<!DOCTYPE html>
<html lang="en">
<head>

    @include(('trainerLayouts.meta'))

    <title>Queue/@yield('title')</title>

    @include('trainerLayouts.css')

    @yield('css')

</head>

<body class="page-body page-fade-only">

<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->


@include('trainerLayouts.nav')

<div class="main-content">
@include('trainerLayouts.header')

    @yield('content')


</div> <!-- /container -->

@include('trainerLayouts.scripts')



@yield('scripts')

    @include('trainerLayouts.bottom')
