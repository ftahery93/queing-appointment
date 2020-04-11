@extends('vendorLayouts.master')

@section('title')
Dashboard
@endsection

@section('css')
{!! Charts::styles() !!}
@endsection

@section('content')
    @if(!$ViewAccess)
   <h2>Not Allowed to Access this Page</h2>
   @else
    @include('fitflowVendor.module2.grid')
     @endif

@endsection

@section('scripts')
{!! Charts::scripts() !!}
{!! $chart->script() !!}
<?php /* ?>
@if($rejectedClasses!=0)
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script>
jQuery(document).ready(function ($)
{
    var opts3 = {
        "closeButton": true,
        "debug": false,
        "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
        "toastClass": "black",
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    toastr.success("You have {{ $rejectedClasses }} Pending Classes for Approval", "", opts3);

});
</script>
@endif
 <?php */ ?>
@endsection