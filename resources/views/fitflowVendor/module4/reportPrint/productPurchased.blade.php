@extends('vendorLayouts.master')

@section('title')
Products Purchased
@endsection


@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM4.'/report/productPurchased') }}">Products Purchased</a>
</li>
@endsection

@section('pageheading')
Products Purchased - Print Preview
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
                            <th class="col-sm-6">Product Name</th>
                            <th class="col-sm-1">Model</th>
                            <th class="text-center col-sm-2">Quantity</th>
                            <th class="col-sm-2 text-right ">Total</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Orders as $Order)
                        <tr>
                            <th class="col-sm-3">{{ $Order->product_name }}</th>   
                            <th class="col-sm-2">{{ $Order->model }}</th>           
                            <th class="col-sm-2">{{ $Order->quantity }}</th> 
                            <th class="col-sm-2 text-right ">{{ $Order->total }}</th> 
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

