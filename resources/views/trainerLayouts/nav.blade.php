<!-- Fixed navbar -->
<div class="sidebar-menu">

    <div class="sidebar-menu-inner">

        <header class="logo-env">

            <!-- logo -->
            <div class="logo">
                <a  href="{{ url('trainer/dashboard') }}">
                     <img src="{{ asset('assets/images/fitflow_logo_white.png') }}" width="120" alt="" />
                </a>
            </div>

            <!-- logo collapse icon -->
            <div class="sidebar-collapse">
                <a href="#" class="sidebar-collapse-icon">
                    <i class="entypo-menu"></i>
                </a>
            </div>



            <div class="sidebar-mobile-menu visible-xs">
                <a href="#" class="with-animation">
                    <i class="entypo-menu"></i>
                </a>
            </div>

        </header>


        <ul id="main-menu" class="main-menu">  
            <li  class="{{ active('trainer/dashboard') }}">
                <a href="{{ url('trainer/dashboard') }}">
                    <i class="entypo-gauge"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            
            <li class="{{ active('trainer/incomeStatistics') }}">
                <a href="{{ url('trainer/incomeStatistics') }}">
                    <i class="fa fa-money"></i>
                    <span class="title">Incomes Statistics</span>
                </a>
            </li>
            
            <li  class="{{ active('trainer/packages') }}">
                <a href="{{ url('trainer/packages') }}">
                    <i class="entypo-bag"></i>
                    <span class="title">Packages</span>
                </a>
            </li>
            
            <li  class="{{ active('trainer/subscribers') }}">
                <a href="{{ url('trainer/subscribers') }}">
                    <i class="entypo-users"></i>
                    <span class="title">Subscribers</span>
                </a>
            </li>
            
            <li class="has-sub  {{ active(['trainer/payments','trainer/subscriptionExpired','trainer/subscriptions','trainer/printPayments','trainer/printSubscriptionExpired','trainer/printSubscriptions','trainer/attendance','trainer/printAttendance'], 'opened') }}">
                <a href="javascript:void(0);">
                    <i class="entypo-chart-pie"></i>
                    <span class="title">Reports</span>
                </a>
                <ul> 
                     <li  class="{{ active(['trainer/payments','trainer/printPayments']) }}">
                        <a href="{{ url('trainer/payments') }}">
                            <i class="fa fa-money"></i>
                            <span class="title">Payments</span>
                        </a>
                    </li>
                   <li  class="{{ active(['trainer/subscriptionExpired','trainer/printSubscriptionExpired']) }}">
                        <a href="{{ url('trainer/subscriptionExpired') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Subscription Expired</span>
                            <span class="badge orange"></span>
                           
                        </a>
                    </li>
                    <li  class="{{ active(['trainer/subscriptions','trainer/printSubscriptions']) }}">
                        <a href="{{ url('trainer/subscriptions') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Subscriptions</span>
                        </a>
                    </li>
                     <li  class="{{ active(['trainer/attendance','trainer/printAttendance']) }}">
                        <a href="{{ url('trainer/attendance') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Attendance</span>
                        </a>
                    </li>
                   
                </ul>
            </li>

        </ul>
        </li>


        </ul>

    </div>

</div>