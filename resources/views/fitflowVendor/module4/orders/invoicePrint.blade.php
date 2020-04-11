@extends('vendorLayouts.master')

@section('title')
Order Invoice
@endsection

@section('css')

@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM4.'/orders')  }}">Orders</a>
</li>
<li>

    <a href="{{ url($configM4.'/order/'.$order_id) }}">Order</a>
</li>

@endsection

@section('pageheading')
Order Invoice
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

                <div style="page-break-after: always;">
                    <h1>Invoice #{{ $Order->invoice_prefix}}{{$Order->invoice_no }}</h1>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td colspan="2">Order Details</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 50%;"><address>
                                        <strong>{{ $Order->vendor }}</strong><br>
                                    </address>
                                    <b>Telephone</b> {{ $Order->mobile }}<br>
                                    <b>E-Mail</b> {{ $Order->email }}<br>                                    
                                    <td style="width: 50%;"><b>Date Added</b> {{ $Order->created_at->format('d/m/Y') }}<br>
                                    <b>Order ID:</b> {{ $order_id }}<br>
                                    <b>Payment Method</b> {{ $Order->payment_method }}<br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @if($Order->pick_from_store==0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td style="width: 50%;"><b>Address</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                    <td class="text-left">{{ $Order->customer_name }}<br>{{ $Order->address_area }}<br>{{ $Order->address_street }}<br>{{ $Order->address_house_building_num }}
                                        <br>{{ $Order->address_avenue }}<br>{{ $Order->address_floor }}<br>{{ $Order->address_flat }}<br>{{ $Order->address_block }}</td>
                                </tr>
                        </tbody>
                    </table>
                    @endif
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td><b>Product</b></td>
                                <td><b>Model</b></td>
                                <td class="text-right"><b>Quantity</b></td>
                                <td class="text-right"><b>Unit Price</b></td>
                                <td class="text-right"><b>Total</b></td>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach ($OrderProducts as $OrderProduct)   
                                <tr>
                                     <td class="text-left">{{ $OrderProduct->name_en }}
                                        <br/>
                                  @if($OrderProductsoption[$OrderProduct->id])
                                  (
                                  <b>Option:
                                    @foreach($OrderProductsoption[$OrderProduct->id] as $val)
                                    @if($loop->iteration!=1)-@endif                                     
                                    <span>
                                      {{ $val->option_name_en }}  
                                    </span>
                                    
                                    @endforeach
                                  )
                                    @endif 
                                    </b>        
                                    </td>
                                    <td class="text-left">{{ $OrderProduct->model }}</td>
                                    <td class="text-right">{{ $OrderProduct->quantity }}</td>
                                    <td class="text-right">{{ $OrderProduct->price }}</td>
                                    <td class="text-right">{{ $OrderProduct->total }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-right">Sub-Total</td>
                                    <td class="text-right">{{ $OrderTotal->sub_total }}</td>
                                </tr>
                                 @if($OrderTotal->coupon_discount!='0.000')
                                <tr>
                                    <td colspan="4" class="text-right">Coupon</td>
                                    <td class="text-right">{{ $OrderTotal->coupon_discount }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-right">Delivery Charge</td>
                                    <td class="text-right">{{ $OrderTotal->delivery_charge }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right">Total</td>
                                    <td class="text-right">{{ $OrderTotal->total }}</td>
                                </tr>
                        </tbody>
                    </table>
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