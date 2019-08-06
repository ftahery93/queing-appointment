@extends('layouts.master')

@section('title')
Customer Orders
@endsection


@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/orderReport/customerOrders') }}">Customer Orders</a>
</li>
@endsection

@section('pageheading')
Customer Orders - Print Preview
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
                            <th class="col-sm-3">Vendor</th>
                            <th class="col-sm-3">Customer Name</th>
                            <th class="col-sm-2">Email</th>
                            <th class="text-center col-sm-1">Mobile</th>
                            <th class="text-center col-sm-1">No. Orders</th>
                            <th class="col-sm-2 text-right ">Total</th>  
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Orders as $Order)

                        <tr>
                            <th class="col-sm-3">{{ $Order->vendor }}</th>   
                            <th class="col-sm-3">{{ $Order->customer_name }}</th>   
                            <th class="col-sm-2">{{ $Order->email }}</th>           
                            <th class="col-sm-1">{{ $Order->mobile }}</th> 
                            <th class="col-sm-1">{{ $Order->num_orders }}</th> 
                            <th class="col-sm-2 text-right ">{{ $Order->total }}</th> 
                        </tr>                                                                                                                                                  

                        @endforeach
                    </tbody>

                </table>
                <div class="row">
                    <div class="col-sm-8 pull-right">
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th><div class="pull-left"><h4><b>Total Orders</b><h4></div> <div class="pull-right"><h4><b>{{ $OrderCount }}</b><h4></div></th>
                                                                <th><div class="pull-left"><h4><b>Total Amount ({{ config('global.amountCurrency') }})</b><h4></div> <div class="pull-right"> <h4><b>{{ $OrderAmountCount }}</b><h4></div></th>
                                                                                            </tr>

                                                                                            </thead>
                                                                                            </table>
                                                                                            </div>
                                                                                            </div>
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
                                                                                                var htmlToPrint = '<link rel="stylesheet" href="{{ asset('assets / css / bootstrap.css') }}">' +
                                                                                                        '<link rel="stylesheet" href="{{ asset('assets / css / print.css') }}">';
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

