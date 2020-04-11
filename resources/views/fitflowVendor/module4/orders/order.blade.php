@extends('vendorLayouts.master')

@section('title')
Orders
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM4.'/orders')  }}">Orders</a>
</li>
@endsection

@section('pageheading')
Orders
@endsection
<div class="row">
    <div class="col-md-12">
        @if(count($errors))
        @include('layouts.flash-message')
        @yield('form-error')
        @endif
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-options padding10">
                    <button Onclick="return ConfirmPrint();" type="button" class="btn btn-info btn-icon">
                        Invoice Preview
                        <i class="entypo-print"></i>
                    </button>   
                </div>
            </div>


            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> Order Details</h3>
                            </div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td style="width: 1%;"><button data-toggle="tooltip" title="" class="btn btn-info btn-xs" data-original-title="Store"><i class="fa fa-shopping-cart fa-fw"></i></button></td>
                                        <td>{{ $Order->vendor }}</td>
                                    </tr>
                                    <tr>
                                        <td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs" data-original-title="Date Added"><i class="fa fa-calendar fa-fw"></i></button></td>
                                        <td>{{ $Order->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs" data-original-title="Payment Method"><i class="fa fa-credit-card fa-fw"></i></button></td>
                                        <td>{{ $Order->payment_method }}</td>
                                    </tr>

                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-user"></i> Customer Details</h3>
                            </div>
                            <table class="table">
                                <tbody><tr>
                                        <td style="width: 1%;"><button data-toggle="tooltip" title="" class="btn btn-info btn-xs" data-original-title="Customer"><i class="fa fa-user fa-fw"></i></button></td>
                                        <td> {{ $Order->customer_name }}</td>
                                    </tr>                                        
                                    <tr>
                                        <td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs" data-original-title="E-Mail"><i class="fa fa-envelope-o fa-fw"></i></button></td>
                                        <td><a href="mailto:{{ $Order->email }}">{{ $Order->email }}</a></td>
                                    </tr>
                                    <tr>
                                        <td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs" data-original-title="Telephone"><i class="fa fa-phone fa-fw"></i></button></td>
                                        <td><a href="tel:{{ $Order->mobile }}">{{ $Order->mobile }}</a></td>
                                    </tr>
                                </tbody></table>
                        </div>
                    </div>

                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-info-circle"></i> Order (#{{ $order_id }})</h3>
                    </div>
                    <div class="panel-body">
                        @if($Order->pick_from_store==0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td style="width: 50%;" class="text-left">Address</td>
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
                                    <td class="text-left">Product</td>
                                    <td class="text-left">Model</td>
                                    <td class="text-right">Quantity</td>
                                    <td class="text-right">Unit Price</td>
                                    <td class="text-right">Total</td>
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
</div>
@endsection

@section('scripts')
<script>
    /*---On Print All Confirmation---*/
    function ConfirmPrint() {
        window.location.href = '{{ url("$configM4/orders/$order_id/orderInvoicePrint") }}';
    }

    /*------END----*/
</script>   
@endsection