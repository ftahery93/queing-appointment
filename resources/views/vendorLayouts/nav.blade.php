<!-- Fixed navbar -->
<div class="sidebar-menu">

    <div class="sidebar-menu-inner">

        <header class="logo-env">

            <!-- logo -->
            <div class="logo">
                <a  href="{{ url($configName.'/home') }}">
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

            <li  class="{{ active($configName.'/home') }}">
                <a href="{{ url($configName.'/home') }}">
                    <i class="entypo-home"></i>
                    <span class="title">Home</span>
                </a>
            </li>
            @if (!str_contains($pathURL['path'], $configM1) && !str_contains($pathURL['path'], $configM2)
            && !str_contains($pathURL['path'], $configM3) && !str_contains($pathURL['path'], $configM4))
            @if (Auth::guard('vendor')->user()->hasRolePermission('dashboard'))
            <li  class="{{ active($configName.'/dashboard') }}">
                <a href="{{ url($configName.'/dashboard') }}">
                    <i class="entypo-gauge"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('incomeStatistics'))
            <li class="{{ active($configName.'/incomeStatistics') }}">
                <a href="{{ url($configName.'/incomeStatistics') }}">
                    <i class="fa fa-money"></i>
                    <span class="title">Incomes Statistics</span>
                </a>
            </li>
            @endif  

            @if (Auth::guard('vendor')->user()->hasRolePermission('vendorBranches'))
            <li class="{{ str_contains(request()->url(), '/vendorBranches') ? 'active' : '' }}">
                <a href="{{ url($configName.'/vendorBranches') }}">
                    <i class="entypo-flow-tree"></i>
                    <span class="title">Branches</span>
                </a>
            </li>
            @endif 

            @if (Auth::guard('vendor')->user()->hasRolePermission('users') || Auth::guard('vendor')->user()->hasRolePermission('permissions'))
            <li class="has-sub @if(str_contains(request()->url(), '/permissions') || str_contains(request()->url(), '/users'))  opened @endif">
                <a href="javascript:void(0);">
                    <i class="entypo-layout"></i>
                    <span class="title">Admin</span>
                </a>
                <ul>
                    @if (Auth::guard('vendor')->user()->hasRolePermission('users'))
                    <li class="{{ str_contains(request()->url(), '/users') ? 'active' : '' }}">

                        <a href="{{ url($configName.'/users') }}">
                            <i class="entypo-user"></i>
                            <span class="title">Users</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('permissions'))
                    <li class="{{ str_contains(request()->url(), '/permissions') ? 'active' : '' }}">
                        <a href="{{ url($configName.'/permissions') }}">
                            <i class="entypo-lock"></i>
                            <span class="title">Users Group</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('logActivity'))
            <li  class="{{ active($configName.'/logActivity') }}">
                <a href="{{ url($configName.'/logActivity') }}">
                    <i class="entypo-clock"></i>
                    <span class="title">Log Activity</span>
                </a>
            </li>
            @endif
            @endif
            <!-- Module 1-->
            @if (str_contains($pathURL['path'], $configM1))
            @if (Auth::guard('vendor')->user()->hasRolePermission('M1Dashboard'))
            <li  class="{{ active($configM1.'/dashboard') }}">
                <a href="{{ url($configM1.'/dashboard') }}">
                    <i class="entypo-gauge"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M1IncomeStatistics'))
            <li class="{{ active($configM1.'/incomeStatistics') }}">
                <a href="{{ url($configM1.'/incomeStatistics') }}">
                    <i class="fa fa-money"></i>
                    <span class="title">Income Statistics</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('vendorPackages'))
            <li  class="{{ str_contains(request()->url(), '/packages') ? 'active' : '' }}">
                <a href="{{ url($configM1.'/packages') }}">
                    <i class="entypo-bag"></i>
                    <span class="title">Packages</span>
                </a>
            </li>
            @endif
             @if (Auth::guard('vendor')->user()->hasRolePermission('instructorPackages'))
            <li  class="{{ str_contains(request()->url(), '/instructorPackages') ? 'active' : '' }}">
                <a href="{{ url($configM1.'/instructorPackages') }}">
                    <i class="entypo-bag"></i>
                    <span class="title">Instructor Packages</span>
                </a>
            </li>
            @endif
             @if (Auth::guard('vendor')->user()->hasRolePermission('instructorSubscriptions'))
            <li  class="{{ str_contains(request()->url(), '/instructorSubscriptions') ? 'active' : '' }}">
                <a href="{{ url($configM1.'/instructorSubscriptions') }}">
                    <i class="entypo-clock"></i>
                    <span class="title">Instructor Subscriptions</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('members'))
            <li  class="{{ str_contains(request()->url(), '/members') ? 'active' : '' }}">
                <a href="{{ url($configM1.'/members') }}">
                    <i class="entypo-users"></i>
                    <span class="title">Members</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('uploadMembers'))
            <li  class="{{ active($configName.'/importexportdata') }}">
                <a href="{{ url($configName.'/importexportdata') }}">
                    <i class="entypo-upload"></i>
                    <span class="title">Upload Members</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('favourites') || Auth::guard('vendor')->user()->hasRolePermission('payments')
            || Auth::guard('vendor')->user()->hasRolePermission('onlinePayments') || Auth::guard('vendor')->user()->hasRolePermission('subscriptionExpired')
            || Auth::guard('vendor')->user()->hasRolePermission('subscriptions')|| Auth::guard('vendor')->user()->hasRolePermission('reportInstructorSubscriptions'))
            <li class="has-sub  {{ active([$configM1.'/favourites',$configM1.'/payments',$configM1.'/onlinePayments',
                        $configM1.'/subscriptionExpired',$configM1.'/subscriptions',$configM1.'/m1/instructorSubscriptions'], 'opened') }}">
                <a href="javascript:void(0);">
                    <i class="entypo-chart-pie"></i>
                    <span class="title">Reports</span>
                </a>
                <ul>  
                    @if (Auth::guard('vendor')->user()->hasRolePermission('favourites'))
                    <li  class="{{ active($configM1.'/favourites') }}">
                        <a href="{{ url($configM1.'/favourites') }}">
                            <i class="fa fa-heart"></i>
                            <span class="title">Favourites</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('payments'))
                    <li  class="{{ active($configM1.'/payments') }}">
                        <a href="{{ url($configM1.'/payments') }}">
                            <i class="fa fa-money"></i>
                            <span class="title">Payments</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('onlinePayments'))
                    <li  class="{{ active($configM1.'/onlinePayments') }}">
                        <a href="{{ url($configM1.'/onlinePayments') }}">
                            <i class="fa fa-money"></i>
                            <span class="title">Online Payments</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('subscriptionExpired'))
                    <li  class="{{ active($configM1.'/subscriptionExpired') }}">
                        <a href="{{ url($configM1.'/subscriptionExpired') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Subscription Expired</span>
                            <span class="badge orange">{{ $memberExpired }}</span>

                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('subscriptions'))
                    <li  class="{{ active($configM1.'/subscriptions') }}">
                        <a href="{{ url($configM1.'/subscriptions') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Subscriptions</span>
                        </a>
                    </li>
                    @endif
                     @if (Auth::guard('vendor')->user()->hasRolePermission('reportInstructorSubscriptions'))
                    <li  class="{{ active($configM1.'/m1/instructorSubscriptions') }}">
                        <a href="{{ url($configM1.'/m1/instructorSubscriptions') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Instructor Subscriptions</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @endif

            <!-- Module 2-->
            @if (str_contains($pathURL['path'], $configM2))
            @if (Auth::guard('vendor')->user()->hasRolePermission('M2dashboard'))
            <li  class="{{ active($configM2.'/m2/dashboard') }}">
                <a href="{{ url($configM2.'/m2/dashboard') }}">
                    <i class="entypo-gauge"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M2incomeStatistics'))
            <li class="{{ active($configM2.'/m2/incomeStatistics') }}">
                <a href="{{ url($configM2.'/m2/incomeStatistics') }}">
                    <i class="fa fa-money"></i>
                    <span class="title">Income Statistics</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('classPackages'))
            <li  class="{{ str_contains(request()->url(), '/classPackages') ? 'active' : '' }}">
                <a href="{{ url($configM2.'/classPackages') }}">
                    <i class="entypo-bag"></i>
                    <span class="title">Packages</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('classMaster'))
            <li  class="{{ str_contains(request()->url(), '/classMaster') ? 'active' : '' }}">
                <a href="{{ url($configM2.'/classMaster') }}">
                    <i class="entypo-bell"></i>
                    <span class="title">Classes</span>
                </a>
            </li>
            <li  class="{{ active($configM2.'/classBranch') }}">
                <a href="{{ url($configM2.'/classBranch') }}">
                    <i class="entypo-bell"></i>
                    <span class="title">Branch wise Classes</span>
                </a>
            </li>
           <?php /* ?>
            <li  class="{{ active($configM2.'/rejectedClasses') }}">
                <a href="{{ url($configM2.'/rejectedClasses') }}">
                    <i class="entypo-bell"></i>
                    <span class="title">Rejected Classses</span>
                    <span class="badge orange">{{ $rejectedClasses }}</span>
                </a>
            </li>
            <?php */ ?>
            @endif

            @if (Auth::guard('vendor')->user()->hasRolePermission('classSchedules'))
            <li  class="{{ active($configM2.'/manageSchedule') }}">
                <a href="{{ url($configM2.'/manageSchedule') }}">
                    <i class="entypo-clock"></i>
                    <span class="title">Manage Schedules</span>
                </a>
            </li>
            <li  class="{{ active($configM2.'/schedules') }}">
                <a href="{{ url($configM2.'/schedules') }}">
                    <i class="entypo-clock"></i>
                    <span class="title">Schedules</span>
                </a>
            </li>
            @endif


            @if (Auth::guard('vendor')->user()->hasRolePermission('M2subscribers'))
            <li  class="{{ active($configM2.'/subscribers') }}">
                <a href="{{ url($configM2.'/subscribers') }}">
                    <i class="entypo-users"></i>
                    <span class="title">Subscribers</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('classBookings') || Auth::guard('vendor')->user()->hasRolePermission('module2OnlinePayments')
            || Auth::guard('vendor')->user()->hasRolePermission('module2SubscriptionExpired') || Auth::guard('vendor')->user()->hasRolePermission('module2Subscriptions'))
            <li class="has-sub  {{ active([$configM2.'/report/bookings',$configM2.'/printBookings',$configM2.'/report/onlinePayments',$configM2.'/reportPrint/onlinePayments',$configM2.'/report/subscriptionExpired'
                ,$configM2.'/reportPrint/subscriptionExpired',$configM2.'/report/subscriptions',$configM2.'/reportPrint/subscriptions'], 'opened') }}">
                <a href="javascript:void(0);">
                    <i class="entypo-chart-pie"></i>
                    <span class="title">Reports</span>
                </a>
                <ul>  
                    @if (Auth::guard('vendor')->user()->hasRolePermission('classBookings'))
                    <li  class="{{ active([$configM2.'/report/bookings',$configM2.'/printBookings']) }}">
                        <a href="{{ url($configM2.'/report/bookings') }}">
                            <i class="fa fa-book"></i>
                            <span class="title">Bookings</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('module2OnlinePayments'))
                    <li  class="{{ active([$configM2.'/report/onlinePayments',$configM2.'/reportPrint/onlinePayments']) }}">
                        <a href="{{ url($configM2.'/report/onlinePayments') }}">
                            <i class="fa fa-money"></i>
                            <span class="title">Payments</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('module2SubscriptionExpired'))
                    <li  class="{{ active([$configM2.'/report/subscriptionExpired',$configM2.'/reportPrint/subscriptionExpired']) }}">
                        <a href="{{ url($configM2.'/report/subscriptionExpired') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Subscription Expired</span>

                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('module2Subscriptions'))
                    <li  class="{{ active([$configM2.'/report/subscriptions',$configM2.'/reportPrint/subscriptions']) }}">
                        <a href="{{ url($configM2.'/report/subscriptions') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Subscriptions</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('uploadSchedule'))
            <li  class="{{ active($configM2.'/uploadSchedule') }}">
                <a href="{{ url($configM2.'/uploadSchedule') }}">
                    <i class="entypo-upload"></i>
                    <span class="title">Upload Schedule</span>
                </a>
            </li>
            @endif
            @endif

            <!-- Module 3-->
            @if (str_contains($pathURL['path'], $configM3))            
            @if (Auth::guard('vendor')->user()->hasRolePermission('M3classSchedules'))           
            <li  class="{{ active($configM3.'/m3/schedules') }}">
                <a href="{{ url($configM3.'/m3/schedules') }}">
                    <i class="entypo-clock"></i>
                    <span class="title">Schedules</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M3subscribers'))
            <li  class="{{ active([$configM3.'/m3/subscribers',$configM3.'/m3/subscribers/.*']) }}">
                <a href="{{ url($configM3.'/m3/subscribers') }}">
                    <i class="entypo-users"></i>
                    <span class="title">Subscribers</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M3bookings'))
            <li  class="{{ active([$configM3.'/m3/report/bookings',$configM3.'/m3/printBookings']) }}">
                <a href="{{ url($configM3.'/m3/report/bookings') }}">
                    <i class="fa fa-book"></i>
                    <span class="title">Bookings</span>
                </a>
            </li>
            @endif
            @endif

            <!---Module4--->
            @if (str_contains($pathURL['path'], $configM4))  
            @if (Auth::guard('vendor')->user()->hasRolePermission('M4Dashboard'))
            <li  class="{{ active($configM4.'/m4/dashboard') }}">
                <a href="{{ url($configM4.'/m4/dashboard') }}">
                    <i class="entypo-gauge"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M4IncomeStatistics'))
            <li class="{{ active($configM4.'/m4/incomeStatistics') }}">
                <a href="{{ url($configM4.'/m4/incomeStatistics') }}">
                    <i class="fa fa-money"></i>
                    <span class="title">Income Statistics</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M4categories'))           
            <li  class="{{ str_contains(request()->url(), '/categories') ? 'active' : '' }}">
                <a href="{{ url($configM4.'/categories') }}">
                    <i class="entypo-tools"></i>
                    <span class="title">Categories</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M4products'))           
            <li  class="{{ str_contains(request()->url(), '/products') ? 'active' : '' }}">
                <a href="{{ url($configM4.'/products') }}">
                    <i class="entypo-basket"></i>
                    <span class="title">Products</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M4options'))           
            <li  class="{{ str_contains(request()->url(), '/options') ? 'active' : '' }}">
                <a href="{{ url($configM4.'/options') }}">
                    <i class="entypo-tag"></i>
                    <span class="title">Options</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('M4orders'))           
            <li  class="{{ str_contains(request()->url(), '/orders') ? 'active' : '' }}">
                <a href="{{ url($configM4.'/orders') }}">
                    <i class="fa fa-cart-arrow-down"></i>
                    <span class="title">Orders</span>
                </a>
            </li>
            @endif
            @if (Auth::guard('vendor')->user()->hasRolePermission('customerOrders') || Auth::guard('vendor')->user()->hasRolePermission('reportCoupons')
             || Auth::guard('vendor')->user()->hasRolePermission('reportOrderPayment')  || Auth::guard('vendor')->user()->hasRolePermission('productPurchased'))
            <li class="has-sub  {{ active([$configM4.'/report/customerOrders',$configM4.'/reportPrint/customerOrders',
                       $configM4.'/report/coupons',$configM4.'/reportPrint/coupons',$configM4.'/report/orderPayments',$configM4.'/reportPrint/orderPayments',
                       $configM4.'/report/productPurchased',$configM4.'/reportPrint/productPurchased'], 'opened') }}">
                <a href="javascript:void(0);">
                    <i class="entypo-chart-pie"></i>
                    <span class="title">Reports</span>
                </a>
                <ul>  
                    @if (Auth::guard('vendor')->user()->hasRolePermission('customerOrders'))
                    <li  class="{{ active([$configM4.'/report/customerOrders',$configM4.'/reportPrint/customerOrders']) }}">
                        <a href="{{ url($configM4.'/report/customerOrders') }}">
                            <i class="fa fa-book"></i>
                            <span class="title">Customer Orders</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::guard('vendor')->user()->hasRolePermission('reportCoupons'))
                    <li  class="{{ active([$configM4.'/report/coupons',$configM4.'/reportPrint/coupons']) }}">
                        <a href="{{ url($configM4.'/report/coupons') }}">
                            <i class="entypo-cc-share"></i>
                            <span class="title">Coupons</span>
                        </a>
                    </li>
                    @endif
                     @if (Auth::guard('vendor')->user()->hasRolePermission('reportOrderPayment'))
                    <li  class="{{ active([$configM4.'/report/orderPayments',$configM4.'/reportPrint/orderPayments']) }}">
                        <a href="{{ url($configM4.'/report/orderPayments') }}">
                           <i class="fa fa-money"></i>
                            <span class="title">Payments</span>
                        </a>
                    </li>
                    @endif
                     @if (Auth::guard('vendor')->user()->hasRolePermission('reportProductPurchased'))
                    <li  class="{{ active([$configM4.'/report/productPurchased',$configM4.'/reportPrint/productPurchased']) }}">
                        <a href="{{ url($configM4.'/report/productPurchased') }}">
                           <i class="entypo-basket"></i>
                            <span class="title">Product Purchased </span>
                        </a>
                    </li>
                    @endif

                </ul>
            </li>
            @endif
            @endif

        </ul>
        </li>


        </ul>

    </div>

</div>