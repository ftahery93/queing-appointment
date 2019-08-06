@extends('layouts.master')

@section('title')
Income Statistics
@endsection

@section('css')
<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
{!! Charts::styles() !!}
@endsection

@section('content')

@section('breadcrumb')

@endsection

@section('pageheading')
Income Statistics
@endsection
<div class="row income_sales sales">

    <div class="col-sm-12">
        <div class="col-sm-10">
            <!--            <label for="field-2" class="col-sm-3 control-label">Date Range</label>-->

            <div class="col-sm-4" id="income_period">
                <select name="report_type" class="select2" data-allow-clear="true" id="report_type" onchange="GetSelectedTextValue(this)" >
                    <option value="1" selected="">Daily</option>
                    <option value="2">Monthly</option>
                </select>
            </div>
            <div class="col-sm-4" id="daily_date">
                <input type="text" class="form-control datetimepicker" id="date" autocomplete="off"  name="date">
            </div>

            <div class="col-sm-6" id="monthly_date" style="display:none;">
                <div class="col-sm-6">
                    <input type="text" class="form-control datetimepicker" id="startdate" autocomplete="off"  name="startdate">
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control datetimepicker" id="enddate" autocomplete="off"  name="enddate">
                </div>
            </div>


            <div class="col-sm-1">
                <button type="button" class="btn btn-light_blue" id="filter"><i class="fa fa-filter"></i> Filter</button>
            </div>
        </div>
        <br />
        <br />
        <br />
    </div>

    <div data-duration="500" class="charts-loader enabled" style="display: none; position: relative; top: 170px; height: 100vh;">
        <center>
            <div class="loading-spinner" style="border: 3px solid #000000; border-right-color: transparent;"></div>
        </center>
    </div>


    <div id="income">
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
    </div>

    <div class="clear visible-xs"></div>
</div>
@endsection

@section('scripts') 
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
{!! Charts::scripts() !!}
{!! $chart->script() !!}  
{!! $chart2->script() !!} 
{!! $chart3->script() !!} 
{!! $chart4->script() !!} 

<!-- Imported scripts on this page -->    
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script type="text/javascript">
                    jQuery(document).ready(function ($)
                    {

                        /*----Chart Month Update---*/
                        $('#filter').on('click', function (e) {
                            e.preventDefault();
                            $('.charts-loader').show();
                            $('#income').css('display', 'none');
                            var daily_date = '0';
                            var startdate = '0';
                            var enddate = '0';
                            var report_type = $('#report_type').val();
                            daily_date = $('#date').val();
                            startdate = $('#startdate').val();
                            enddate = $('#enddate').val();
                            $.ajax({
                                type: "POST",
                                async: true,
                                url: "{{ url('admin/incomeStatistics') }}",
                                data: {daily_date: daily_date, report_type: report_type, startdate: startdate, enddate: enddate, _token: '{{ csrf_token() }}'},
                                success: function (data) {
                                    if (data.success) {
                                        $('#income').css('display', 'block');
                                        $('#income').html(data.html);
                                    } else {
                                        $('#income').css('display', 'block');
                                        toastr.error(data.response, "", opts);
                                    }
                                },
                                complete: function () {
                                    $('.charts-loader').hide();
                                }
                            });
                        });

                        /*------END----*/
                        // Sample Toastr Notification
                        var opts = {
                            "closeButton": true,
                            "debug": false,
                            "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
                            "toastClass": "error",
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "timeOut": "5000",
                            "extendedTimeOut": "8000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        };

                    });
</script>
<script>


</script>
<script>
    function GetSelectedTextValue(val) {
        var report_type = val.value;
        if (report_type == 1) {
            $('#daily_date').show();
            $('#monthly_date').hide();
            return false;
        } else {
            $('#daily_date').hide();
            $('#monthly_date').show();
            return false;
        }
    }
    $(function () {
        /*-------Date-----------*/
        $('#date').datepicker({
            format: 'mm/yyyy',
            startView: "months",
            minViewMode: "months",
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        });
        $('#startdate').datepicker({
            format: 'mm/yyyy',
            startView: "months",
            minViewMode: "months",
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        }).on('changeDate', function (selected) {
            startDate = new Date(selected.date.valueOf());
            startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
            $('#enddate').datepicker('setStartDate', startDate);
        });
        $('#enddate').datepicker({
            format: 'mm/yyyy',
            startView: "months",
            minViewMode: "months",
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        }).on('changeDate', function (selected) {
            FromEndDate = new Date(selected.date.valueOf());
            FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
            $('#startdate').datepicker('setEndDate', FromEndDate);
        });
    });
</script>


@endsection