   <div class="col-sm-12" id="income_box">
        <div class="col-sm-4">			
            <div class="tile-stats tile-neon">
                <div class="num" data-start="0" data-end="{{ $totalAmount }}"  data-postfix="{{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $totalAmount }} {{ config('global.amountCurrency') }}</div>					
                <h3>Total Sales</h3>					
            </div>				
        </div>

        <div class="col-sm-4">			
            <div class="tile-stats tile-neon">
                <div class="num" data-start="0" data-end="{{ $totalAdminCommission }}"  data-postfix="{{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $totalAdminCommission }} {{ config('global.amountCurrency') }}</div>					
                 <h3>Admin Commission</h3>				
            </div>				
        </div>

        <div class="col-sm-4">
            <div class="tile-stats tile-neon">
                <div class="num" data-start="0" data-end="{{ $totalProfit }}"  data-postfix="{{ config('global.amountCurrency') }}" data-duration="1500" data-delay="0">{{ $totalProfit }} {{ config('global.amountCurrency') }}</div>
                 <h3>Total Profit</h3>
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
<!--            <li>
                <a href="#fitflow_m" data-toggle="tab">
                    <span class="visible-xs"><i class="entypo-users"></i></span>
                    <span class="hidden-xs"><i class="entypo-users"></i> {{ $module3->name_en }}</span>
                </a>
            </li>-->
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
                                <h2> {{ $module1TotalAdminCommission }}</h2>                      
                            </div>

                            <div class="tile-progressbar">
                                <span data-fill="100%"></span>
                            </div>

                            <div class="tile-footer">
                                <h4>
                                    <i class="fa fa-money"></i> Total Admin Commission
                                </h4>
                            </div>
                        </div>





                        <div class="tile-progress orange">

                            <div class="tile-header">
                                <h2> {{ $module1TotalProfit }}</h2>                      
                            </div>

                            <div class="tile-progressbar">
                                <span data-fill="100%"></span>
                            </div>

                            <div class="tile-footer">
                                <h4>
                                    <i class="fa fa-money"></i> Total Profit
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
                                <h2> {{ $module2TotalAdminCommission }}</h2>                      
                            </div>

                            <div class="tile-progressbar">
                                <span data-fill="100%"></span>
                            </div>

                            <div class="tile-footer">
                                <h4>
                                    <i class="fa fa-money"></i>  Total Admin Commission
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
                                    <i class="fa fa-money"></i> Total Profit
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
                                <h2>1000</h2>                   
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
                                <h2> 550</h2>                      
                            </div>

                            <div class="tile-progressbar">
                                <span data-fill="100%"></span>
                            </div>

                            <div class="tile-footer">
                                <h4>
                                    <i class="fa fa-money"></i>   Amount to be Transfer to Vendor
                                </h4>
                            </div>
                        </div>





                        <div class="tile-progress orange">

                            <div class="tile-header">
                                <h2> 450</h2>                      
                            </div>

                            <div class="tile-progressbar">
                                <span data-fill="100%"></span>
                            </div>

                            <div class="tile-footer">
                                <h4>
                                    <i class="fa fa-money"></i>  Total Trainer Profit
                                </h4>

                            </div>
                        </div>



                        <div class="clear visible-xs"> </div>

                    </div>

                    <div class="col-sm-8">
                        <div id="container3"></div>

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



                        <div class="tile-progress magenta">

                            <div class="tile-header">
                                <h2> {{ $module4TotalAdminCommission }}</h2>                      
                            </div>

                            <div class="tile-progressbar">
                                <span data-fill="100%"></span>
                            </div>

                            <div class="tile-footer">
                                <h4>
                                    <i class="fa fa-money"></i>  Total Admin Commission
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
                                    <i class="fa fa-money"></i> Total Profit
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
{!! $chart4->script() !!}  