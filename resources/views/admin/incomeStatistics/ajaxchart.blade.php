
<div class="col-sm-12" id="income_box">
    <div class="col-sm-3">			
        <div class="tile-stats tile-neon">
            <div class="num" data-start="0" data-end="{{ $totalAmount }}"  data-postfix=" {{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $totalAmount }}</div>					
            <h3>Total Sales</h3>					
        </div>				
    </div>

    <div class="col-sm-3">			
        <div class="tile-stats tile-neon">
            <div class="num" data-start="0" data-end="{{ $vendorAmount }}"  data-postfix=" {{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $vendorAmount }}</div>					
            <h3>Vendors Amount</h3>					
        </div>				
    </div>

    <div class="col-sm-3">

        <div class="tile-stats tile-neon">
            <div class="num" data-start="0" data-end="{{ $trainerAmount }}"  data-postfix=" {{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $trainerAmount }}</div>

            <h3>Trainers Amount</h3>

        </div>

    </div>

    <div class="col-sm-3">			
        <div class="tile-stats tile-neon">
            <div class="num" data-start="0" data-end="{{ $totalProfit }}"  data-postfix=" {{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $totalProfit }}</div>					
            <h3>Net Profit</h3>					
        </div>				
    </div>        


    <br />
</div>
<div class="col-sm-12" id="income_box">
            <div class="col-sm-3">			
                <div class="tile-stats tile-neon">
                    <div class="num" data-start="0" data-end="{{ $totalDeliverycharge }}"  data-postfix=" {{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $totalDeliverycharge }}</div>					
                    <h3>Total Delivery Charge</h3>					
                </div>				
            </div>

            <div class="col-sm-3">			
                <div class="tile-stats tile-neon">
                    <div class="num" data-start="0" data-end="{{ $totalCoupon }}"  data-postfix=" {{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $totalCoupon }}</div>					
                    <h3>Total Coupon Discount</h3>					
                </div>				
            </div>

            <br />
        </div>
<div class="col-sm-12">
    <ul class="nav nav-tabs bordered">
        <li class="active">
            <a href="#subscription" data-toggle="tab">
                <span class="visible-xs"><i class="entypo-arrows-ccw"></i></span>
                <span class="hidden-xs"><i class="entypo-arrows-ccw"></i> {{ $module1->name_en }}</span>
            </a>
        </li>

        <li>
            <a href="#classes" data-toggle="tab">
                <span class="visible-xs"><i class="entypo-clock"></i></span>
                <span class="hidden-xs"><i class="entypo-clock"></i> {{ $module2->name_en }}</span>
            </a>
        </li>
        <li>
            <a href="#fitflow_m" data-toggle="tab">
                <span class="visible-xs"><i class="entypo-users"></i></span>
                <span class="hidden-xs"><i class="entypo-users"></i> {{ $module3->name_en }}</span>
            </a>
        </li>
        <li>
            <a href="#e_store" data-toggle="tab">
                <span class="visible-xs"><i class="entypo-bag"></i></span>
                <span class="hidden-xs"><i class="entypo-bag"></i> {{ $module4->name_en }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="subscription">

            <div class="row">
                <div class="col-sm-4 sales">


                    <div class="tile-progress neon">

                        <div class="tile-header">
                            <h2>{{ $module1TotalAmount }}</h2>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Sales
                            </h4>
                        </div>
                    </div>



                    <div class="tile-progress magenta">

                        <div class="tile-header">
                            <h2> {{ $module1TotalVendorAmount }}</h2>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Vendors Amount
                            </h4>
                        </div>
                    </div>





                    <div class="tile-progress orange">

                        <div class="tile-header">
                            <h2> {{ $module1TotalTrainerAmount }}</h2>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i> Total Trainers Amount
                            </h4>

                        </div>
                    </div>

                    <div class="tile-progress aqua_blue">

                        <div class="tile-header">
                            <h2> {{ $module1TotalProfit }}</h2>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i> Net Profit
                            </h4>

                        </div>
                    </div>



                    <div class="clear visible-xs"> </div>

                </div>

                <div class="col-sm-8">                       
                    <center>                          
                        {!! $chart->html() !!}
                    </center>

                </div>
            </div>
        </div>
        <div class="tab-pane" id="classes">
            <div class="row">
                <div class="col-sm-4 sales">


                    <div class="tile-progress neon">

                        <div class="tile-header">
                            <h2>{{ $module2TotalAmount }}</h2>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Sales
                            </h4>
                        </div>
                    </div>



                    <div class="tile-progress magenta">

                        <div class="tile-header">
                            <h2> {{ $module2TotalVendorAmount }}</h2>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Vendors Amount
                            </h4>
                        </div>
                    </div>





                    <div class="tile-progress orange">

                        <div class="tile-header">
                            <h2> {{ $module2TotalProfit }}</h2>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>   Net Profit
                            </h4>

                        </div>
                    </div>



                    <div class="clear visible-xs"> </div>

                </div>

                <div class="col-sm-8">                       
                    <center>                          
                        {!! $chart2->html() !!}
                    </center>

                </div>
            </div>

        </div>
        <div class="tab-pane" id="fitflow_m">
            <div class="row">
                <div class="col-sm-4 sales">


                    <div class="tile-progress neon">

                        <div class="tile-header">
                            <h2>{{ $module3TotalAmount }}</h2>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Sales
                            </h4>
                        </div>
                    </div>



                    <div class="clear visible-xs"> </div>

                </div>

                <div class="col-sm-8">                       
                    <center>                          
                        {!! $chart3->html() !!}
                    </center>

                </div>
            </div>
        </div>

        <div class="tab-pane" id="e_store">
            <div class="row">
                <div class="col-sm-4 sales">


                    <div class="tile-progress neon">

                        <div class="tile-header">
                            <h2>{{ $module4TotalAmount }}</h2>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Sales
                            </h4>
                        </div>
                    </div>

                    <div class="tile-progress tile-brown">

                        <div class="tile-header">
                            <h2> {{ $totalDeliverycharge }}</h2>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Delivery charge
                            </h4>
                        </div>
                    </div>

                    <div class="tile-progress tile-purple">

                        <div class="tile-header">
                            <h2>{{ $totalCoupon }}</h2>                   
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Coupon Discount
                            </h4>
                        </div>
                    </div>

                    <div class="tile-progress magenta">

                        <div class="tile-header">
                            <h2> {{ $module4TotalVendorAmount }}</h2>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>  Total Vendors Amount
                            </h4>
                        </div>
                    </div>





                    <div class="tile-progress orange">

                        <div class="tile-header">
                            <h2> {{ $module4TotalProfit }}</h2>                      
                        </div>

                        <div class="tile-progressbar">
                            <span data-fill="100%"></span>
                        </div>

                        <div class="tile-footer">
                            <h4>
                                <i class="fa fa-money"></i>   Net Profit
                            </h4>

                        </div>
                    </div>



                    <div class="clear visible-xs"> </div>

                </div>

                <div class="col-sm-8">                       
                    <center>                          
                        {!! $chart4->html() !!}
                    </center>

                </div>
            </div>

        </div>
    </div>

</div>

{!! $chart->script() !!}  
{!! $chart2->script() !!} 
{!! $chart3->script() !!} 
{!! $chart4->script() !!} 