@extends('layouts.master')

@section('title')
Bookings
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/classBookings') }}">Bookings</a>
</li>
@endsection

@section('pageheading')
Subscriptions - Print Preview
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">   


            <div class="panel-body  table-responsive" id="printTable">
                <button  type="button" class="btn btn-success btn-icon pull-right no-print" id="btn">
                    Print 
                    <i class="entypo-print"></i>
                </button>
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                 <br/>
                     <br/>
               <div class="row margin10">
                <div class="col-sm-12">
                    <div class="col-sm-6 pull-right">
                        <button type="button" class="btn btn-info btn-xs pull-right" style="font-size:15px;margin-left:10px;">Bookings: <span id="Count" >{{ $Count }}</span> </button> 
                    </div>
                </div>
            </div>
                      <br/>
                     <br/>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-2">Subscriber</th>   
                            <th class="col-sm-2">Class Name</th>                            
                            <th class="col-sm-2 text-center">Start Time</th>
                            <th class="col-sm-2 text-center">End Time</th>                            
                            <th class="col-sm-2 text-center">Schedule Date</th>
                            <th class="col-sm-1 text-center">Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookingHistory as $booking)

                        <tr>
                            <th class="col-sm-2">{{ $booking->name }}</th>   
                            <th class="col-sm-2">{{ $booking->class_name }}</th>                            
                            <th class="col-sm-2 text-center">{{ Carbon\Carbon::parse($booking->start)->format('h:m:A') }}</th>
                            <th class="col-sm-2 text-center">{{ Carbon\Carbon::parse($booking->end)->format('h:m:A') }}</th>                            
                            <th class="col-sm-2 text-center">{{ Carbon\Carbon::parse($booking->schedule_date)->format('d/m/Y') }}</th>
                            <th class="col-sm-1 text-center">{{ Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') }}</th>
                        </tr>                                                                                                                                                  

                        @endforeach
                    </tbody>

                </table>


            </div>

        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
    $(window).load(function(){
    $('.loading-image').hide();
    });
    });
    /*---On Print All Confirmation---*/
    $("#btn").click(function () {
    printData();
    });
    /*------END----*/
    function printData()
    {
    var divToPrint = document.getElementById("printTable");
    var htmlToPrint = '<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">' +
            '<link rel="stylesheet" href="{{ asset('assets/css/print.css') }}">';
    htmlToPrint += divToPrint.outerHTML;
    newWin = window.open("");
    newWin.document.write(htmlToPrint);
    newWin.print();
    newWin.close();
    }
</script>  
@endsection