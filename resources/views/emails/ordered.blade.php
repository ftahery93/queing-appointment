<!DOCTYPE html>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <style type="text/css" rel="stylesheet" media="all">
            /* Media Queries */
            @media  only screen and (max-width: 500px) {
                .button {
                    width: 100% !important;
                }
            }
            body{font-family:sans-serif;color: #74787E;}
            .break_div{
                margin-bottom: 20px;
                display: block;
                float: left;
                width: 100%;
            }
            .text-left{text-align: left;}
            .text-right{text-align: right;}
            .text-center{text-align: center;}
            table.product  tr td{border:1px solid #dddd;border-collapse: collapse;}
            table tr td{padding:5px;}
             table.product {
                border-collapse: collapse;
            }
        </style>
    </head>



    <body style="margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- Logo -->
                        <tr>
                            <td style="padding: 25px 0; text-align: center;">
                                <a style="font-size: 16px; font-weight: bold; color: #2F3133; text-decoration: none; text-shadow: 0 1px 0 white;" href="http://localhost:8000" target="_blank">
                                    {{ config('app.name') }}
                                </a>
                            </td>
                        </tr>

                        <!-- Email Body -->
                        <tr>
                            <td style="width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;" width="100%">
                                <table style="width: 100%; max-width: 1000px; margin: 0 auto; padding: 0;" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:20px;">
                                         
                                            <!-- Action Button -->
                                            <table style="width: 100%; margin: 30px auto; padding: 0; text-align: center;" align="center" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td align="center">
                                                        <div id="rec-wrapper-inv">

                                                            <div class="titleblock">                              

                                                                <img src="{{ asset('assets/images/fitflow_logo_white.png') }}" style="width:80px;">

                                                                <div class="pull-right"><h3></h3></div>  
                                                            </div>



                                                            <div class="row-fluid" style="margin-top:10px;">     
                                                                <div class="row">                                                                   
                                                                    <div class="break_div">
                                                                        <table class="table" style="width:100%;float:left;border:1px solid #ddd;">
                                                                            <thead>
                                                                                <tr colspan="2">
                                                                                    <td> <h3 class="panel-title">Order Details</h3></td>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>
                                                                                        <table>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td>Vendor Name</td>
                                                                                                    <td>{{ $Order->vendor }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>Date Added</td>
                                                                                                    <td>{{ $Order->created_at->format('d/m/Y') }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>Payment Method</td>
                                                                                                    <td>{{ $Order->payment_method }}</td>
                                                                                                </tr>   
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                    <td>
                                                                                        <table>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td>Customer Name</td>
                                                                                                    <td> {{ $Order->customer_name }}</td>
                                                                                                </tr> 
                                                                                                 <tr>
                                                                                                    <td>Order ID</td>
                                                                                                    <td> {{ $Order->id }}</td>
                                                                                                </tr> 
                                                                                                <tr>
                                                                                                    <td>E-Mail</td>
                                                                                                    <td><a href="mailto:{{ $Order->email }}">{{ $Order->email }}</a></td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>Telephone</td>
                                                                                                    <td><a href="tel:{{ $Order->mobile }}">{{ $Order->mobile }}</a></td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>


                                                                            </tbody>

                                                                        </table>
                                                                    </div>


                                                                    @if($Order->pick_from_store==0)                                                                         <br/>

                                                                    <table class="table table-bordered" style="width:50%;float:left;border:1px solid #ddd;">
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
                                                                    <div class="break_div">
                                                                        <table class="table table-bordered product" style="width:100%;float:left;border:1px solid #ddd;">
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
                                                                                @foreach ($Order->OrderProducts as $OrderProduct)                                
                                                                                <tr>
                                                                                    <td class="text-left">{{ $OrderProduct->name_en }}
                                                                                        @if($Order->OrderProductsoption[$OrderProduct->id])
                                                                                        (
                                                                                        Option:
                                                                                        @foreach($Order->OrderProductsoption[$OrderProduct->id] as $val)
                                                                                        @if($loop->iteration!=1),@endif                                     
                                                                                        <span>
                                                                                            {{ $val->option_name_en }}  
                                                                                        </span>

                                                                                        @endforeach
                                                                                        )
                                                                                        @endif 

                                                                                    </td>
                                                                                    <td class="text-left">{{ $OrderProduct->model }}</td>
                                                                                    <td class="text-right">{{ $OrderProduct->quantity }}</td>
                                                                                    <td class="text-right">{{ $OrderProduct->price }}</td>
                                                                                    <td class="text-right">{{ $OrderProduct->total }}</td>
                                                                                </tr>
                                                                                @endforeach
                                                                                <tr>
                                                                                    <td colspan="4" class="text-right">Sub-Total</td>
                                                                                    <td class="text-right">{{ $Order->OrderTotal->sub_total }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="4" class="text-right">Delivery Charge</td>
                                                                                    <td class="text-right">{{ $Order->OrderTotal->delivery_charge }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="4" class="text-right">Total</td>
                                                                                    <td class="text-right">{{ $Order->OrderTotal->total }}</td>
                                                                                </tr>
                                                                            </tbody>

                                                                        </table>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <!-- Outro -->

                                            <!-- Salutation -->
                                            <p style="margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;">
                                                Regards,<br>{{ config('app.name') }}
                                            </p>

                                            <!-- Sub Copy -->
                                            <table style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;">
                                                <tr>
                                                    <td>
                                                        <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                                            If you’re having trouble clicking the "Application Link" button,
                                                            copy and paste the URL below into your web browser:
                                                        </p>

                                                        <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                                            <a style="color: #3869D4;" href="http://localhost:8000/admin" target="_blank">
                                                                {{ url('admin') }}
                                                            </a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td>
                                <table style="width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="color: #AEAEAE; padding: 35px; text-align: center;">
                                            <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                                &copy; 2018
                                                <a style="color: #3869D4;" href="{{ url('admin') }}" target="_blank">{{ config('app.name') }}</a>.
                                                All rights reserved.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
