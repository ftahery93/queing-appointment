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
    @include('fitflowVendor.module4.grid')
     @endif

@endsection

@section('scripts')
{!! Charts::scripts() !!}
{!! $chart->script() !!}

@endsection