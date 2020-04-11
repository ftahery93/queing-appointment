@extends('vendorLayouts.master')

@section('title')
Instructor Subscriptions
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

    <a href="{{ url($configM1.'/m1/instructorSubscriptions') }}">Instructor Subscriptions</a>
</li>
@endsection

@section('pageheading')
Instructor Subscriptions - Print Preview
@endsection

<div class="row">
    <div class="col-md-12">
        @include('vendorLayouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">   


            <div class="panel-body  table-responsive" id="printTable">
                <button  type="button" class="btn btn-success btn-icon pull-right no-print" id="btn">
                    Print 
                    <i class="entypo-print"></i>
                </button>
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <br/>
                <br/>
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-4">Subscriber</th>
                            <th class="text-center col-sm-1">Mobile</th> 
                            <th class="col-sm-1">Package</th>                                                               
                            <th class="text-right col-sm-1">Price</th>
                            <th class="text-center col-sm-2">No. Sessions</th>
                            <th class="text-center col-sm-1">Booked</th>
                            <th class="text-center col-sm-2">Subscribed On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($InstructorSubscription as $Subscriber)

                        <tr>
                            <td>{{ $Subscriber->subscriber }}</td>
                            <td  class="text-center">{{ $Subscriber->mobile }}</td>
                            <td>{{ $Subscriber->package_name }}</td>
                            <td  class="text-right">{{ $Subscriber->price }}</td>
                            <td  class="text-center">{{ $Subscriber->num_points }}</td>
                            <td  class="text-center">{{ $Subscriber->num_booked }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($Subscriber->created_at)->format('d/m/Y') }}</td>
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