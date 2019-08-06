@extends('layouts.master')

@section('title')
Payments
@endsection


@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/orderReport/orderPayments') }}">Payments</a>
</li>
@endsection

@section('pageheading')
Payments - Print Preview
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

                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                             <th class="col-sm-2">Vendor</th>
                            <th class="col-sm-2">Customer Name</th>
                            <th class="col-sm-1">Order ID</th>                            
                            <th class="col-sm-2">Reference ID</th>                          
                            <th class="col-sm-1 text-right">Amount {{ config('global.amountCurrency') }}</th>
                            <th class="col-sm-2 text-center">Collected On</th>
                            <th class="col-sm-2 text-center">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Orders as $Order)

                        <tr>
                            <th class="col-sm-2">{{ $Order->vendor }}</th>   
                            <th class="col-sm-2">{{ $Order->user }}</th>   
                            <th class="col-sm-1">{{ $Order->order_id }}</th>           
                            <th class="col-sm-2">{{ $Order->reference_id }}</th>
                            <th class="col-sm-1 text-right ">{{ $Order->amount }}</th> 
                            <td  class="text-center col-sm-2">{{ Carbon\Carbon::parse($Order->post_date)->format('d/m/Y') }}</td>
                            <td class="col-sm-2">{{ $Order->payment_method }}</td>
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
//         +
//        '<style type="text/css">' +
//        '.no-print{' +
//        'display: none !important;' +
//       // 'padding;0.5em;' +
//        '}' +
//        '</style>';

// var htmlToPrint = '' +
//        '<style type="text/css">' +
//        '.no-print{' +
//        'display: none !important;' +
//       // 'padding;0.5em;' +
//        '}' +
//        '</style>';

    htmlToPrint += divToPrint.outerHTML;
    newWin = window.open("");
    newWin.document.write(htmlToPrint);
    newWin.print();
    newWin.close();
    }
</script> 
@endsection

