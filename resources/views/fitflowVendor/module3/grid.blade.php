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
                <h3 class="text-center">{{ $totalSubscribers }} </h3>

            </div>

            <div class="tile-progressbar">
                <span data-fill="100%"></span>
            </div>

            <div class="tile-footer">
                <h4>
                    Total Subscribers
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
                <div class="panel-title">Top {{ count($topPackages) }} Most Selling Packages</div>

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
                @if(array_key_exists('2', $topPackages))
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
                @endif
            </div>

        </div>
    </div>

</div>
<br />
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
            <!-- panel head -->
            <div class="panel-heading">
                <div class="panel-title">Top 5 {{ $appTitle->title }} Classes Report</div>

                <!--                <div class="panel-options">						
                                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                
                                </div>                -->
            </div>

            <div class="panel-body no-padding">

                <div class="col-sm-12 margin10">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th class="text-center">Classes</th>
                                <th class="col-sm-10 text-center">No. of Booking</th>
                            </tr>
                        </thead> 
                        <tbody>
                            <tr>
                                <td  class="text-center">
                                    Wrestling
                                </td>
                                <td>
                                    <table  class="table table-bordered datatable dashboard_class_table col-sm-6">
                                        <tbody>
                                            <tr>
                                                <td class="col-sm-6"> Al Asimah Governorate (Capital) <span class="badge orange" style="color:#fff;">3</span> </td>
                                                <td class="col-sm-6"> Hawalli Governorate <span class="badge orange" style="color:#fff;">4</span> </td>

                                            </tr>
                                            <tr>
                                                <td  class="col-sm-6"> Farwaniya Governorate <span class="badge orange" style="color:#fff;">5</span> </td>
                                                <td  class="col-sm-6"> Mubarak Al-Kabeer Governorate <span class="badge orange" style="color:#fff;">1</span> </td>

                                            </tr>
                                            <tr>
                                                <td  class="col-sm-6"> Ahmadi Governorate <span class="badge orange" style="color:#fff;">3</span> </td>
                                                <td  class="col-sm-6">Jahra Governorate <span class="badge orange" style="color:#fff;">4</span> </td>

                                            </tr>
                                        </tbody> 
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td  class="text-center">
                                    Boxing
                                </td>
                                <td>
                                    <table  class="table table-bordered datatable dashboard_class_table col-sm-6">
                                        <tbody>
                                            <tr>
                                                <td class="col-sm-6"> Al Asimah Governorate (Capital) <span class="badge orange" style="color:#fff;">4</span> </td>
                                                <td  class="col-sm-6"> Farwaniya Governorate <span class="badge orange"n style="color:#fff;">2</span> </td>

                                            </tr>

                                        </tbody> 
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td  class="text-center">
                                    Squats
                                </td>
                                <td>
                                    <table  class="table table-bordered datatable dashboard_class_table col-sm-6">
                                        <tbody>
                                            <tr>
                                                <td class="col-sm-6"> Mubarak Al-Kabeer Governorate <span class="badge orange" style="color:#fff;">7</span> </td>                                              
                                            </tr>

                                        </tbody> 
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td  class="text-center">
                                    Swimming
                                </td>
                                <td>
                                    <table  class="table table-bordered datatable dashboard_class_table col-sm-6">
                                        <tbody>
                                            <tr>
                                                <td class="col-sm-6"> Al Asimah Governorate (Capital) <span class="badge orange" style="color:#fff;">3</span> </td>
                                                <td class="col-sm-6"> Hawalli Governorate <span class="badge orange" style="color:#fff;">4</span> </td>

                                            </tr>
                                            <tr>
                                                <td  class="col-sm-6"> Farwaniya Governorate <span class="badge orange" style="color:#fff;">5</span> </td>
                                                <td  class="col-sm-6"> Mubarak Al-Kabeer Governorate <span class="badge orange" style="color:#fff;">1</span> </td>

                                            </tr>
                                            <tr>
                                                <td  class="col-sm-6"> Ahmadi Governorate <span class="badge orange" style="color:#fff;">3</span> </td>
                                                <td  class="col-sm-6">Jahra Governorate <span class="badge orange" style="color:#fff;">4</span> </td>

                                            </tr>
                                        </tbody> 
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td  class="text-center">
                                    Fatburn
                                </td>
                                <td>
                                    <table  class="table table-bordered datatable dashboard_class_table col-sm-6">
                                        <tbody>
                                            <tr>
                                                <td class="col-sm-6"> Mubarak Al-Kabeer Governorate <span class="badge orange" style="color:#fff;">2</span> </td>                                              
                                            </tr>

                                        </tbody> 
                                    </table>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    
</div>
<br />
<div class="row users_list">
   
    <div class="col-sm-12 col-md-12 col-lg-12">
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
                            <th>Name</th>
                            <th>Package Name</th>
                            <th class="text-center">Duration</th>
                            <th class="text-right">Amount (KD)</th>
                            <th class="text-center">Payment Type</th>
                            <th class="text-center">Collected On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($PaymentDetail as $Payment)

                        <tr>
                            <td>{{ $Payment->user }}</td>
                            <td>{{ $Payment->package }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($Payment->start_date)->format('d/m/Y') }}-{{ Carbon\Carbon::parse($Payment->end_date)->format('d/m/Y') }}</td>
                            <td class="text-right">{{ $Payment->amount }}</td>
                            <td class="text-center">{{ $Payment->card_type }}</td> 
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