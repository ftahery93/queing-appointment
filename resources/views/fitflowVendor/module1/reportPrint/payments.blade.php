@extends('vendorLayouts.master')

@section('title')
Payments
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

    <a href="{{ url($configM1.'/payments') }}">Payments</a>
</li>
@endsection

@section('pageheading')
Payments - Print Preview
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
                            <th class="col-sm-2 text-center">Invoice No.</th>
                            <th class="col-sm-3">Name</th>
                            <th class="col-sm-3">Package Name</th>
                            <th class="col-sm-1 text-center">Collected On</th>
                            <th class="col-sm-1">Collected By</th>                          
                            <th class="col-sm-1 text-right">Cash {{ config('global.amountCurrency') }}</th>
                            <th class="col-sm-1 text-right">KNET {{ config('global.amountCurrency') }}</th>
                            <th class="col-sm-1 text-right">Fee {{ config('global.amountCurrency') }}</th>
                        </tr>
                    </thead>
<tbody>
                        @foreach ($Invoices as $Invoice)

                        <tr>
                            <td  class="text-center">{{ $Invoice->receipt_num }}</td>
                            <td>{{ $Invoice->name }}</td>
                            <td>{{ $Invoice->package_name }}</td>
                            <td>{{ Carbon\Carbon::parse($Invoice->created_at)->format('d/m/Y') }}</td>
                            <td>{{ $Invoice->collected_by }}</td>
                            <td>{{ $Invoice->cash }}</td>
                            <td>{{ $Invoice->knet }}</td>
                            <td>{{ $Invoice->price }}</td>
                        </tr>                                                                                                                                                  

                        @endforeach
                    </tbody>

                </table>
                <div class="row">
                    <div class="col-sm-8 pull-right">
                        <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th><div class="pull-left"><h4><b>Total Amount ({{ config('global.amountCurrency') }})</b><h4></div> <div class="pull-right"><h4><b>{{ $invoiceAmount->fees }}</b><h4></div></th>
                            <th><div class="pull-left"><h4><b>Total Cash ({{ config('global.amountCurrency') }})</b><h4></div> <div class="pull-right"> <h4><b>{{ $invoiceAmount->cash_amount }}</b><h4></div></th>
                             <th><div class="pull-left"><h4><b>Total KNET ({{ config('global.amountCurrency') }})</b><h4> </div> <div class="pull-right"><h4><b>{{ $invoiceAmount->knet_amount }}</b><h4></div></th>
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
   var divToPrint=document.getElementById("printTable");
   
 var htmlToPrint ='<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">'  +
         '<link rel="stylesheet" href="{{ asset('assets/css/print.css') }}">';
    htmlToPrint += divToPrint.outerHTML;
   newWin= window.open("");
   newWin.document.write(htmlToPrint);
   newWin.print();
   newWin.close();
}
</script>  
@endsection