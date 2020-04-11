@extends('vendorLayouts.master')

@section('title')
Home
@endsection

@section('css')

@endsection

@section('content')
    @include('fitflowVendor.grid')

@endsection

@section('scripts')
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