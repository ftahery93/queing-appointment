<div class="row sales">

    <div class="col-sm-3">

        <div class="tile-progress magenta">

            <div class="tile-header">
                <table width="100%">
                    <tr><td>Total Sales  <h3>{{ number_format($M1totalAmount, config('global.decimalValue')) }} 
                                <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3></td>
                                <td style="text-align:right;">Total Profit<h3>{{ number_format($M1totalProfit, config('global.decimalValue')) }} 
                                        <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3></td></tr> 

                </table>

            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            <div class="tile-footer">
                <h4>
                    {{ $module1->name_en }}
                </h4>


            </div>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-progress orange">

            <div class="tile-header">
                <table width="100%">
                    <tr><td>Total Sales  <h3>{{ number_format($M2totalAmount, config('global.decimalValue')) }}
                               <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3></td>
                               <td style="text-align:right;">Total Profit<h3>{{ number_format($M2totalProfit, config('global.decimalValue')) }}
                                       <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3></td></tr> 

                </table>

            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            <div class="tile-footer">
                <h4>
                    {{ $module2->name_en }}
                </h4>


            </div>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-progress aqua_blue">

            <div class="tile-header">
                <table width="100%">
                 <tr><td>Total Profit  <h3>{{ number_format($totalBookedClassProfit, config('global.decimalValue')) }}
                           <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3></td><td style="text-align:right;">Total Bookings<h3>{{ $totalBookings }} </h3></td></tr> 

                </table>

            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            <div class="tile-footer">
                <h4>
                    {{ $module3->name_en }}
                </h4>


            </div>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-progress neon">

            <div class="tile-header">
                 <table width="100%">
                    <tr><td>Total Sales  <h3>{{ number_format($M4totalAmount, config('global.decimalValue')) }} <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3></td>
                        <td style="text-align:right;">Total Profit<h3>{{ number_format($M4totalProfit, config('global.decimalValue')) }} <span class="kd_span">{{ config('global.amountCurrency') }}</span> </h3></td></tr> 

                </table>
            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            
            <div class="tile-footer">
                 <h4>
                    {{ $module4->name_en }}
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
                <div class="panel-title">
                    Top {{ count($topPackages) }}
                    Most Selling Packages</div>

                <!--                <div class="panel-options">						
                                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                
                                </div>-->
            </div>

            <!-- panel body -->
            <div class="panel-body no-padding">
                @if (Auth::guard('vendor')->user()->hasRolePermission('vendorPackages'))
                @if(array_key_exists('0', $topPackages))
                <div class="col-sm-12 margin10">

                    <div class="tile-progress neon">

                        <div class="tile-header">
                            <h4> {{ $topPackages[0]->name_en }}</h4>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topPackages[0]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topPackages[0]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @if(array_key_exists('1', $topPackages))
                <div class="col-sm-12">

                    <div class="tile-progress magenta">

                        <div class="tile-header">
                            <h4> {{ $topPackages[1]->name_en }}</h4>                       
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topPackages[1]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topPackages[1]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @if(array_key_exists('2', $topPackages))
                <div class="col-sm-12">

                    <div class="tile-progress orange">

                        <div class="tile-header">
                            <h4> {{ $topPackages[2]->name_en }}</h4>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topPackages[2]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topPackages[2]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                @if(array_key_exists('3', $topPackages))
                <div class="col-sm-12">

                    <div class="tile-progress sky_blue">

                        <div class="tile-header">
                            <h4> {{ $topPackages[3]->name_en }}</h4>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topPackages[3]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topPackages[3]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                 @if(array_key_exists('4', $topPackages))
                <div class="col-sm-12">

                    <div class="tile-progress sky_blue">

                        <div class="tile-header">
                            <h4> {{ $topPackages[4]->name_en }}</h4>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topPackages[4]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topPackages[4]->sold }}
                            </span>


                        </div>
                    </div>

                </div>
                @endif
                 @if(array_key_exists('5', $topPackages))
                <div class="col-sm-12">

                    <div class="tile-progress sky_blue">

                        <div class="tile-header">
                            <h4> {{ $topPackages[5]->name_en }}</h4>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <span>Amount {{ $topPackages[5]->amount }}  {{ config('global.amountCurrency') }}</span>
                            <span>
                                Total Sold: {{ $topPackages[5]->sold }}
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
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Top {{ $topM2ClassCount }}  Classes Bookings</div>

            </div>

            <div class="panel-body no-padding">
                @if (Auth::guard('vendor')->user()->hasRolePermission('classes'))
                <div class="col-sm-12 margin10">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th class="text-center">Classes</th>
                                <th class="col-sm-10 text-center">No. of Booking</th>
                            </tr>
                        </thead> 
                        <tbody>
                            @foreach ($topM2ClassArray as $key=>$class)
                            <tr>
                                <td  class="text-center">
                                    {{ $key }}
                                </td>
                                <td>
                                    <table  class="table table-bordered datatable dashboard_class_table col-sm-6">
                                        <tbody>
                                            @foreach ($class->chunk(2) as $chunk)                                            
                                            <tr>
                                                @foreach ($chunk as $val)
                                                <td class="col-sm-6">  {{ $val->governorate }} <span class="badge orange" style="color:#fff;">{{ $val->class_count }}</span> </td>
                                                @endforeach
                                            </tr>
                                            @endforeach

                                        </tbody> 
                                    </table>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
    
  <div class="col-sm-6">
        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">{{ $module3->name_en }} - Top {{ $topM3ClassCount }}  Classes Bookings</div>

            </div>

            <div class="panel-body no-padding">
                @if (Auth::guard('vendor')->user()->hasRolePermission('classes'))
                <div class="col-sm-12 margin10">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th class="text-center">Classes</th>
                                <th class="col-sm-10 text-center">No. of Booking</th>
                            </tr>
                        </thead> 
                        <tbody>
                            @foreach ($topM3ClassArray as $key=>$class)
                            <tr>
                                <td  class="text-center">
                                    {{ $key }}
                                </td>
                                <td>
                                    <table  class="table table-bordered datatable dashboard_class_table col-sm-6">
                                        <tbody>
                                            @foreach ($class->chunk(2) as $chunk)                                            
                                            <tr>
                                                @foreach ($chunk as $val)
                                                <td class="col-sm-6">  {{ $val->governorate }} <span class="badge orange" style="color:#fff;">{{ $val->class_count }}</span> </td>
                                                @endforeach
                                            </tr>
                                            @endforeach

                                        </tbody> 
                                    </table>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
<br />
<div class="row users_list">
    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Latest Members</div>

                <!--                <div class="panel-options">						
                                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                
                                </div>-->
            </div>

            <!-- panel body -->
            <div class="panel-body">
                @if (Auth::guard('vendor')->user()->hasRolePermission('members'))

                <table width="100%" border="0" cellspacing="10" cellpadding="10" style="border-collapse: separate;border-spacing:0 10px;">
                    @foreach ($Members->chunk(8) as $chunk)

                    <tr>
                        @foreach ($chunk as $Member)
                        <td> <a href="{{ url($configM1.'/members/'.$Member->id.'/edit') }}">  
                                <img  src="{{ asset('assets/images/user_noimage.png') }}" alt="" class="img-circle fit-circle-img" width="60">
                                <h6>{{ $Member->name }}</h6></a></td>
                        @endforeach
                    </tr>                   
                    @endforeach
                    @endif
                </table>

            </div>

        </div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Top Subscribers</div>

                <!--                <div class="panel-options">						
                                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                
                                </div>-->
            </div>

            <!-- panel body -->
            <div class="panel-body">

                <table width="100%" border="0" cellspacing="10" cellpadding="10" style="border-collapse: separate;border-spacing:0 10px;">
                    @foreach ($topBuyers->chunk(8) as $chunk)

                    <tr>
                        @foreach ($chunk as $topBuyers)
                        <td>
                            <img  src="{{ asset('assets/images/user_noimage.png') }}" alt="" class="img-circle fit-circle-img" width="60">
                            <h6>{{ $topBuyers->name }}</h6></td>
                        @endforeach
                    </tr>                   
                    @endforeach

                </table>
            </div>

        </div>
    </div>

</div>
<br />
<div class="row">

    <div class="col-sm-12 col-md-8 col-lg-8">

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

    <div class="col-sm-4 dashboard_packages">
        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">
                   Top  {{ count($topProducts) }} 
                    Most Selling Products</div>

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

    <div class="clear visible-xs"></div>
</div>

<br />
<div class="row">

    <div class="col-sm-12 col-md-12 col-lg-8">

        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Latest Online Payments</div>

                <!--                <div class="panel-options">						
                                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                
                                </div>-->
            </div>

            <!-- panel body -->
            <div class="panel-body">
                <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                    <li class="active">
                        <a href="#subscription" data-toggle="tab">
                            <span class="visible-xs"><i class="entypo-home"></i></span>
                            <span class="hidden-xs">Subscription / Renewal</span>
                        </a>
                    </li>

                    <li>
                        <a href="#classes" data-toggle="tab">
                            <span class="visible-xs"><i class="entypo-mail"></i></span>
                            <span class="hidden-xs">Classes</span>
                        </a>
                    </li>
<!--                    <li>
                        <a href="#fitflow_m" data-toggle="tab">
                            <span class="visible-xs"><i class="entypo-cog"></i></span>
                            <span class="hidden-xs">{{ $appTitle->title }} Membership</span>
                        </a>
                    </li>-->
                    <li>
                        <a href="#e_store" data-toggle="tab">
                            <span class="visible-xs"><i class="entypo-cog"></i></span>
                            <span class="hidden-xs">E-store</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="subscription">

                        <div class="scrollable" data-height="400">

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Package Name</th>
                                        <th class="text-center">Duration</th>
                                        <th class="text-right">Amount (KD)</th>
                                        <th class="text-center">Payment Type</th>
                                        <th class="text-center">Collected On</th>
                                    </tr>
                                </thead>

                                @foreach ($M1PaymentDetail as $Payment)
                                <tbody>
                                    <tr>
                                        <td>{{ $Payment->user }}</td>
                                        <td>{{ $Payment->package }}</td>
                                        <td class="text-center">{{ Carbon\Carbon::parse($Payment->start_date)->format('d/m/Y') }}-{{ Carbon\Carbon::parse($Payment->end_date)->format('d/m/Y') }}</td>
                                        <td class="text-right">{{ $Payment->amount }}</td>
                                        <td class="text-center">{{ $Payment->card_type }}</td> 
                                        <td>{{ Carbon\Carbon::parse($Payment->post_date)->format('d/m/Y') }}</td>
                                    </tr>                                                                                                                                                  

                                </tbody>
                                @endforeach
                            </table>
                        </div>

                    </div>

                    <div class="tab-pane" id="classes">
                          <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Package Name</th>
                                        <th class="text-center">Duration</th>
                                        <th class="text-right">Amount (KD)</th>
                                        <th class="text-center">Payment Type</th>
                                        <th class="text-center">Collected On</th>
                                    </tr>
                                </thead>

                                @foreach ($M2PaymentDetail as $Payment)
                                <tbody>
                                    <tr>
                                        <td>{{ $Payment->user }}</td>
                                        <td>{{ $Payment->package }}</td>
                                        <td class="text-center">{{ Carbon\Carbon::parse($Payment->start_date)->format('d/m/Y') }}-{{ Carbon\Carbon::parse($Payment->end_date)->format('d/m/Y') }}</td>
                                        <td class="text-right">{{ $Payment->amount }}</td>
                                        <td class="text-center">{{ $Payment->card_type }}</td> 
                                        <td>{{ Carbon\Carbon::parse($Payment->post_date)->format('d/m/Y') }}</td>
                                    </tr>                                                                                                                                                  

                                </tbody>
                                @endforeach
                            </table>
                    </div>

                   
                    <div class="tab-pane" id="e_store">
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
                                @foreach ($M4PaymentDetail as $Payment)
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

        </div>

    </div>

    <div class="col-sm-12 col-md-12 col-lg-4">
        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Recent Activities</div>

                <!--                <div class="panel-options">						
                                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                
                                </div>-->
            </div>

            <!-- panel body -->
            <div class="panel-body">

                <ul class="activity-list">
                    @if (Auth::guard('vendor')->user()->hasRolePermission('logActivity'))
                    @foreach ($LogActivity as $Activity)
                    <li class="item row margin-btm10">
                        <!--                                <div class="activity-img col-sm-1 col-md-1 col-lg-2">
                                                            <img src="assets/images/thumb-1@2x.png" alt="Product Image" width="39">
                                                        </div>-->
                        <div class="activityinfo col-sm-12 col-md-12 col-lg-12">

                            <span class="product-description">
                                {{ explode(']', $Activity->subject, 2)[1] }} created on {{ $Activity->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </li>
                    @endforeach

                </ul>
                @if ($logCount!=0)
                <div class="col-sm-12">
                    <a type="button" href="{{ url('logActivity') }}" class="btn btn-primary pull-right">View All</a>
                </div>  
                @endif
                @endif
            </div>

        </div>
    </div> 
    <div class="clear visible-xs"></div>
</div>


<div class="row">

    <div class="col-sm-12 col-md-12 col-lg-12">

        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Latest Invoices</div>

                <!--                <div class="panel-options">						
                                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                
                                </div>-->
            </div>

            <!-- panel body -->
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="col-sm-2 text-center">Invoice No.</th>
                            <th class="col-sm-2">Name</th>
                            <th class="col-sm-2">Package Name</th>
                            <th class="col-sm-1 text-center">Collected On</th>
                            <th class="col-sm-1">Collected By</th>                          
                            <th class="col-sm-1 text-right">Cash {{ config('global.amountCurrency') }}</th>
                            <th class="col-sm-1 text-right">KNET {{ config('global.amountCurrency') }}</th>
                            <th class="col-sm-2 text-right">Fee {{ config('global.amountCurrency') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoiceList as $invoice)

                        <tr>
                            <td class="text-center">{{ $invoice->receipt_num }}</td>
                            <td>{{ $invoice->name }}</td>
                            <td>{{ $invoice->package_name }}</td>
                            <td>{{ Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $invoice->collected_by }}</td>
                            <td class="text-right">{{ $invoice->cash }}</td>
                            <td class="text-right">{{ $invoice->knet }}</td>
                            <td class="text-right">{{ $invoice->price }}</td>
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

