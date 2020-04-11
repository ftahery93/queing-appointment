@extends('trainerLayouts.master')

@section('title')
Dashboard
@endsection

@section('css')
{!! Charts::styles() !!}
@endsection

@section('content')
    @include('trainer.grid')

@endsection

@section('scripts')
{!! Charts::scripts() !!}
{!! $chart->script() !!} 


@endsection