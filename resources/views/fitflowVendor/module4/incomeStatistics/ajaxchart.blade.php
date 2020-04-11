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
                <center>                          
                    {!! $chart->html() !!}
                </center>

            </div>

{!! $chart->script() !!}  