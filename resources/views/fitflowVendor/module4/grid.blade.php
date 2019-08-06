<div class="row sales">

    <div class="col-sm-3">

        <div class="tile-progress magenta">

            <div class="tile-header">
                <h3 class="text-center">{{ number_format($totalAmount, config('global.decimalValue')) }} <span class="kd_span">{{ config('global.amountCurrency') }}</span></h3>
            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            <div class="tile-footer">
                <h4>
                    Total Sales
                </h4>


            </div>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-progress orange">

            <div class="tile-header">
                <h3 class="text-center">{{ number_format($totalProfit, config('global.decimalValue')) }} <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3>

            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            <div class="tile-footer">
                <h4>
                    Total Profit 
                </h4>


            </div>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-progress neon">

            <div class="tile-header">
                <h3 class="text-center">{{ number_format($totalAdminCommission, config('global.decimalValue')) }}  <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3>
            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            <div class="tile-footer">
                <h4>
                    Total Admin Commission
                </h4>


            </div>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-progress aqua_blue">

            <div class="tile-header">
                <h3 class="text-center">{{ $totalOrders }} </h3>

            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            <div class="tile-footer">
                <h4>
                    Total Orders
                </h4>


            </div>
        </div>

    </div>



    <div class="clear visible-xs"></div>

</div>
<br />
<div class="row">
    <div class="col-md-8">

        <div class="panel panel-primary" data-collapsed="0">


            <!-- panel body -->
            <div class="panel-body">

                <div class="col-sm-12">
                    <center>                          
                        {!! $chart->html() !!}
                    </center>
                </div>


            </div>

        </div>

    </div>

    <div class="col-sm-4 dashboard_packages">
        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Top {{ count($topProducts) }} Most Selling Products</div>

                <!--                <div class="panel-options">						
                                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                
                                </div>-->
            </div>

            <!-- panel body -->
            <div class="panel-body no-padding">
                @if (Auth::guard('vendor')->user()->hasRolePermission('vendorProducts'))
                @if(array_key_exists('0', $topProducts))
                <div class="col-sm-12 margin10">

                    <div class="tile-progress neon">

                        <div class="tile-header">
                            <h4> {{ $topProducts[0]->name_en }}</h4>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topProducts[0]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topProducts[0]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @if(array_key_exists('1', $topProducts))
                <div class="col-sm-12">

                    <div class="tile-progress magenta">

                        <div class="tile-header">
                            <h4> {{ $topProducts[1]->name_en }}</h4>                       
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topProducts[1]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topProducts[1]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @if(array_key_exists('2', $topProducts))
                <div class="col-sm-12">

                    <div class="tile-progress orange">

                        <div class="tile-header">
                            <h4> {{ $topProducts[2]->name_en }}</h4>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topProducts[2]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topProducts[2]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @if(array_key_exists('3', $topProducts))
                <div class="col-sm-12">

                    <div class="tile-progress sky_blue">

                        <div class="tile-header">
                            <h4> {{ $topProducts[3]->name_en }}</h4>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topProducts[3]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topProducts[3]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @if(array_key_exists('4', $topProducts))
                <div class="col-sm-12">

                    <div class="tile-progress sky_blue">

                        <div class="tile-header">
                            <h4> {{ $topProducts[4]->name_en }}</h4>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topProducts[4]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topProducts[4]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @if(array_key_exists('5', $topProducts))
                <div class="col-sm-12">

                    <div class="tile-progress sky_blue">

                        <div class="tile-header">
                            <h4> {{ $topProducts[5]->name_en }}</h4>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topProducts[5]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topProducts[5]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @endif
            </div>

        </div>
    </div>

</div>

<br />

<br />



<div class="row">

    <div class="col-sm-12 col-md-12 col-lg-12">

        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Latest Online Payments</div>

                <!-- <div class="panel-options">						
                        <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
    
                    </div>-->
            </div>

            <!-- panel body -->
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Order Id</th>
                            <th>Product Name</th>
                            <th class="text-right">Amount (KD)</th>
                            <th class="text-center">Payment Method</th>
                            <th class="text-center">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($PaymentDetail as $Payment)
                        <tr>
                            <td>{{ $Payment->user }}</td>
                            <td>{{ $Payment->id }}</td>
                            <td>{{ $Payment->name_en }}</td>
                            <td class="text-right">{{ $Payment->amount }}</td>
                            <td class="text-center">{{ $Payment->payment_method }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($Payment->post_date)->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>


    <div class="clear visible-xs"></div>
</div>

<br />

<div class="row">
     <div class="col-sm-12 col-md-12 col-lg-12">

        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Latest Orders</div>
            </div>

            <!-- panel body -->
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="col-sm-1 text-center">Order No.</th>                            
                            <th class="col-sm-3">Product Name</th>
                            <th class="col-sm-2">Customer Name</th>
                            <th class="col-sm-2">Customer Email</th>
                            <th class="col-sm-1">Customer Mobile</th>                          
                            <th class="col-sm-2 text-right">Total {{ config('global.amountCurrency') }}</th>
                            <th class="col-sm-1 text-center">Ordered Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($latestOrders as $latestOrder)

                        <tr>
                            <td class="text-center">{{ $latestOrder->order_id }}</td>
                            <td>{{ $latestOrder->name_en }}</td>
                            <td>{{ $latestOrder->name }}</td>                            
                            <td>{{ $latestOrder->email }}</td>
                            <td>{{ $latestOrder->mobile }}</td>
                            <td class="text-right">{{ $latestOrder->total }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($latestOrder->created_at)->format('d/m/Y') }}</td>
                        </tr>                                                                                                                                                  

                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>