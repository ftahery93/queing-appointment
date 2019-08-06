@extends('vendorLayouts.master')

@section('title')
Dashboard
@endsection

@section('css')
{!! Charts::styles() !!}

@endsection

@section('content')
    @include('fitflowVendor.dashboard.grid')

@endsection

@section('scripts')
{!! Charts::scripts() !!}
{!! $chart->script() !!} 
@endsection