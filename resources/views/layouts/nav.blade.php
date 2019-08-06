<!-- Fixed navbar -->
<div class="sidebar-menu">

    <div class="sidebar-menu-inner">

        <header class="logo-env">

            <!-- logo -->
            <div class="logo">
                <a  @if (Auth::user()->hasRolePermission('dashboard')) href="{{ url('admin/dashboard') }}" @else href="javascript:void(0);" @endif>
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
            @if (Auth::user()->hasRolePermission('dashboard'))
            <li  class="{{ active('admin/dashboard') }}">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="entypo-gauge"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->hasRolePermission('incomeStatistics'))
            <li class="{{ active('admin/incomeStatistics') }}">
                <a href="{{ url('admin/incomeStatistics') }}">
                    <i class="fa fa-money"></i>
                    <span class="title">Income Statistics</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->hasRolePermission('orders'))
            <li class="{{ str_contains(request()->url(), '/orders') ? 'active' : '' }}">
                <a href="{{ url('admin/orders') }}">
                    <i class="fa fa-cart-arrow-down"></i>
                    <span class="title">Orders</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->hasRolePermission('registeredUsers'))
            <li class="{{ active('registeredUsers.*') }}">
                <a href="{{ url('admin/registeredUsers') }}" >
                    <i class="entypo-users"></i>
                    <span class="title">Registered Users</span>
                    <?php /* ?><span class="badge orange">{{ $registered_users_records_count }}</span><?php */ ?>
                </a>
            </li>
            @endif 
            @if (Auth::user()->hasRolePermission('packages') || Auth::user()->hasRolePermission('vendorPackages') || Auth::user()->hasRolePermission('classPackages') || Auth::user()->hasRolePermission('trainerPackages'))
            <li class="has-sub {{ active(['packages.*','admin/vendorPackages','admin/classPackages','admin/trainerPackages'], 'opened') }}">
                <a href="javascript:void(0);">
                    <i class="entypo-bag"></i>
                    <span class="title">Packages</span>
                </a>
                <ul>                   
                    @if (Auth::user()->hasRolePermission('vendorPackages'))
                    <li class="{{ active('admin/vendorPackages') }}">
                        <a href="{{ url('admin/vendorPackages') }}">
                            <i class="entypo-briefcase"></i>
                            <span class="title">Vendor Packages</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasRolePermission('instructorPackages'))
                    <li  class="{{ active('admin/instructorPackages') }}">
                        <a href="{{ url('admin/instructorPackages') }}">
                            <i class="entypo-bag"></i>
                            <span class="title">Instructor Packages</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasRolePermission('trainerPackages'))
                    <li class="{{ active('trainerPackages.*') }}">
                        <a href="{{ url('admin/trainerPackages') }}">
                            <i class="entypo-briefcase"></i>
                            <span class="title">Trainer Packages</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasRolePermission('classPackages'))
                    <li class="{{ active('classPackages.*') }}">
                        <a href="{{ url('admin/classPackages') }}">
                            <i class="entypo-briefcase"></i>
                            <span class="title">Class Packages</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasRolePermission('packages'))
                    <li class="{{ active('packages.*') }}">
                        <a href="{{ url('admin/packages') }}">
                            <i class="entypo-briefcase"></i>
                            <span class="title">{{ $appTitle->title }} Packages</span>
                        </a>
                    </li>
                    @endif

                </ul>
            </li>
            @endif

            @if (Auth::user()->hasRolePermission('vendors'))           
            <li  class="{{ active('vendors.*') }}">
                <a href="{{ url('admin/vendors') }}">
                    <i class="entypo-users"></i>
                    <span class="title">Vendors</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->hasRolePermission('trainers'))
            <li  class="{{ active('trainers.*') }}">
                <a href="{{ url('admin/trainers') }}">
                    <i class="entypo-user"></i>
                    <span class="title">Trainers</span>
                    <?php /* ?><span class="badge orange">{{ $trainer_records_count }}</span><?php */ ?>
                </a>
            </li>
            @endif 
            <?php /* ?>
              @if (Auth::user()->hasRolePermission('pendingClasses'))
              <li  class="{{ active('pendingClasses.*') }}">
              <a href="{{ url('admin/pendingVendorClasses') }}">
              <i class="entypo-clock"></i>
              <span class="title">Pending Classes</span>
              <span class="badge orange">{{ $pendingClasses }}</span>
              </a>
              </li>
              @endif
              <?php */ ?>
            @if (Auth::user()->hasRolePermission('classes'))
            <li  class="{{ active('module2ClassSchedules.*') }}">
                <a href="{{ url('admin/module2ClassSchedules') }}">
                    <i class="entypo-clock"></i>
                    <span class="title">Classes</span>
                </a>
            </li>
            @endif 
            @if (Auth::user()->hasRolePermission('vendorfavourites') || Auth::user()->hasRolePermission('vendorPayments')
            || Auth::user()->hasRolePermission('vendorOnlinePayments')  || Auth::user()->hasRolePermission('VendorSubscriptionExpired')
            || Auth::user()->hasRolePermission('VendorSubscriptions')  || Auth::user()->hasRolePermission('classSubscriptions')
            || Auth::user()->hasRolePermission('classSubscriptionExpired')  || Auth::user()->hasRolePermission('classOnlinePayments')
            || Auth::user()->hasRolePermission('classBookings')  || Auth::user()->hasRolePermission('fitflowMembershipSubscriptions')
            || Auth::user()->hasRolePermission('fitflowMembershipSubscriptionExpired')  || Auth::user()->hasRolePermission('fitflowMembershipOnlinePayments')
            || Auth::user()->hasRolePermission('fitflowMembershipBookings'))
            <li class="has-sub  {{ active(['admin/vendorFavourites','admin/vendorPayments','admin/vendorOnlinePayments','admin/vendorSubscriptionExpired','admin/vendorSubscriptions'
            ,'admin/printVendorFavourites','admin/printVendorPayments','admin/printVendorOnlinePayments','admin/printVendorSubscriptionExpired','admin/printVendorSubscriptions',
                'admin/classOnlinePayments','admin/fitflowMembershipOnlinePayments','admin/classSubscriptionExpired'
             ,'admin/fitflowMembershipSubscriptionExpired','admin/classSubscriptions','admin/fitflowMembershipSubscriptions'
         ,'admin/classBookings','admin/fitflowMembershipBookings'], 'opened') }}">
                <a href="javascript:void(0);">
                    <i class="entypo-chart-pie"></i>
                    <span class="title">Vendors Reports</span>
                </a>
                <ul>
                    @if (Auth::user()->hasRolePermission('vendorfavourites'))
                    <li  class="{{ active(['admin/vendorFavourites','admin/printVendorFavourites']) }}">
                        <a href="{{ url('admin/vendorFavourites') }}">
                            <i class="fa fa-heart"></i>
                            <span class="title">Favourites</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasRolePermission('vendorPayments'))
                    <li  class="{{ active(['admin/vendorPayments','admin/printVendorPayments']) }}">
                        <a href="{{ url('admin/vendorPayments') }}">
                            <i class="fa fa-money"></i>
                            <span class="title">Payments</span>
                        </a>
                    </li>
                    @endif
                     @if (Auth::user()->hasRolePermission('reportInstructorSubscriptions'))
                    <li  class="{{ active('admin/m1/instructorSubscriptions') }}">
                        <a href="{{ url('admin/m1/instructorSubscriptions') }}">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Instructor Subscriptions</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasRolePermission('vendorOnlinePayments')|| Auth::user()->hasRolePermission('classOnlinePayments')
                    || Auth::user()->hasRolePermission('fitflowMembershipOnlinePayments'))
                    <li  class="{{ active(['admin/vendorOnlinePayments','admin/printVendorOnlinePayments'
                ,'admin/classOnlinePayments','admin/printVendorOnlinePayments'
            ,'admin/fitflowMembershipOnlinePayments','admin/printVendorOnlinePayments']) }}">

                        @if (Auth::user()->hasRolePermission('vendorOnlinePayments'))
                        <a href="{{ url('admin/vendorOnlinePayments') }}">
                            @elseif (Auth::user()->hasRolePermission('classOnlinePayments')) 
                            <a href="{{ url('admin/classOnlinePayments') }}">
                                @elseif (Auth::user()->hasRolePermission('fitflowMembershipOnlinePayments'))
                                <a href="{{ url('admin/fitflowMembershipOnlinePayments') }}">
                                    @endif   
                                    <i class="fa fa-money"></i>
                                    <span class="title">Online Payments</span>
                                </a>
                                </li>
                                @endif
                                @if (Auth::user()->hasRolePermission('vendorSubscriptionExpired')|| Auth::user()->hasRolePermission('classSubscriptionExpired')
                                || Auth::user()->hasRolePermission('fitflowMembershipSubscriptionExpired'))
                                <li  class="{{ active(['admin/vendorSubscriptionExpired','admin/printVendorSubscriptionExpired'
           ,'admin/classSubscriptionExpired','admin/printVendorSubscriptionExpired'
       ,'admin/fitflowMembershipSubscriptionExpired','admin/printVendorSubscriptionExpired']) }}">
                                    @if (Auth::user()->hasRolePermission('vendorSubscriptionExpired'))
                                    <a href="{{ url('admin/vendorSubscriptionExpired') }}">
                                        @elseif (Auth::user()->hasRolePermission('classSubscriptionExpired')) 
                                        <a href="{{ url('admin/classSubscriptionExpired') }}">
                                            @elseif (Auth::user()->hasRolePermission('fitflowMembershipSubscriptionExpired'))
                                            <a href="{{ url('admin/fitflowMembershipSubscriptionExpired') }}">
                                                @endif 

                                                <i class="fa fa-clock-o"></i>
                                                <span class="title">Subscription Expired</span>
                                                <span class="badge orange"></span>

                                            </a>
                                            </li>
                                            @endif
                                            @if (Auth::user()->hasRolePermission('vendorSubscriptions')|| Auth::user()->hasRolePermission('classSubscriptions')
                                            || Auth::user()->hasRolePermission('fitflowMembershipSubscriptions'))
                                            <li  class="{{ active(['admin/vendorSubscriptions','admin/printVendorSubscriptions'
        ,'admin/classSubscriptions','admin/printVendorSubscriptions'
    ,'admin/fitflowMembershipSubscriptions','admin/printVendorSubscriptions']) }}">
                                                @if (Auth::user()->hasRolePermission('vendorSubscriptions'))
                                                <a href="{{ url('admin/vendorSubscriptions') }}">
                                                    @elseif (Auth::user()->hasRolePermission('classSubscriptions')) 
                                                    <a href="{{ url('admin/classSubscriptions') }}">
                                                        @elseif (Auth::user()->hasRolePermission('fitflowMembershipSubscriptions'))
                                                        <a href="{{ url('admin/fitflowMembershipSubscriptions') }}">
                                                            @endif 
                                                            <i class="fa fa-clock-o"></i>
                                                            <span class="title">Subscriptions</span>
                                                        </a>
                                                        </li>
                                                        @endif
                                                        @if (Auth::user()->hasRolePermission('classBookings')|| Auth::user()->hasRolePermission('fitflowMembershipBookings'))
                                                        <li  class="{{ active(['admin/classBookings','admin/printVendorSubscriptions'
                             ,'admin/fitflowMembershipBookings','admin/printVendorSubscriptions']) }}">
                                                            @if (Auth::user()->hasRolePermission('classBookings'))
                                                            <a href="{{ url('admin/classBookings') }}">
                                                                @elseif (Auth::user()->hasRolePermission('fitflowMembershipBookings')) 
                                                                <a href="{{ url('admin/fitflowMembershipBookings') }}">
                                                                    @endif 
                                                                    <i class="fa fa-book"></i>
                                                                    <span class="title">Bookings</span>
                                                                </a>
                                                        </li>
                                                        @endif

                                                        </ul>
                                                        </li>
                                                        @endif
                                                        @if (Auth::user()->hasRolePermission('trainerpayments') || Auth::user()->hasRolePermission('trainerSubscriptionExpired')
                                                        || Auth::user()->hasRolePermission('trainerSubscriptions'))
                                                        <li class="has-sub  {{ active(['admin/trainerPayments','admin/trainerSubscriptionExpired','admin/trainerSubscriptions','admin/printTrainerPayments'
             ,'admin/printTrainerSubscriptionExpired','admin/printTrainerSubscriptions'], 'opened') }}">
                                                            <a href="javascript:void(0);">
                                                                <i class="entypo-chart-pie"></i>
                                                                <span class="title">Trainer Reports</span>
                                                            </a>
                                                            <ul> 
                                                                @if (Auth::user()->hasRolePermission('trainerpayments'))
                                                                <li  class="{{ active(['admin/trainerPayments','admin/printTrainerPayments']) }}">
                                                                    <a href="{{ url('admin/trainerPayments') }}">
                                                                        <i class="fa fa-money"></i>
                                                                        <span class="title">Payments</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('trainerSubscriptionExpired'))
                                                                <li  class="{{ active(['admin/trainerSubscriptionExpired','admin/printTrainerSubscriptionExpired']) }}">
                                                                    <a href="{{ url('admin/trainerSubscriptionExpired') }}">
                                                                        <i class="fa fa-clock-o"></i>
                                                                        <span class="title">Subscription Expired</span>
                                                                        <span class="badge orange"></span>

                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('trainerSubscriptions'))
                                                                <li  class="{{ active(['admin/trainerSubscriptions','admin/printTrainerSubscriptions']) }}">
                                                                    <a href="{{ url('admin/trainerSubscriptions') }}">
                                                                        <i class="fa fa-clock-o"></i>
                                                                        <span class="title">Subscriptions</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                            </ul>
                                                        </li>
                                                        @endif

                                                        @if (Auth::user()->hasRolePermission('customerOrders') || Auth::user()->hasRolePermission('reportCoupons')
                                                        || Auth::user()->hasRolePermission('reportOrderPayment')  || Auth::user()->hasRolePermission('productPurchased'))
                                                        <li class="has-sub  {{ active(['admin/orderReport/customerOrders','admin/orderReportPrint/customerOrders',
                       'admin/orderReport/coupons','admin/orderReportPrint/coupons','admin/orderReport/orderPayments','admin/orderReportPrint/orderPayments',
                       'admin/orderReport/productPurchased','admin/orderReportPrint/productPurchased'], 'opened') }}">
                                                            <a href="javascript:void(0);">
                                                                <i class="entypo-chart-pie"></i>
                                                                <span class="title">Order Reports</span>
                                                            </a>
                                                            <ul>  
                                                                @if (Auth::user()->hasRolePermission('customerOrders'))
                                                                <li  class="{{ active(['admin/orderReport/customerOrders','admin/orderReportPrint/customerOrders']) }}">
                                                                    <a href="{{ url('admin/orderReport/customerOrders') }}">
                                                                        <i class="fa fa-book"></i>
                                                                        <span class="title">Customer Orders</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('reportCoupons'))
                                                                <li  class="{{ active(['admin/orderReport/coupons','admin/orderReportPrint/coupons']) }}">
                                                                    <a href="{{ url('admin/orderReport/coupons') }}">
                                                                        <i class="entypo-cc-share"></i>
                                                                        <span class="title">Coupons</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('reportOrderPayment'))
                                                                <li  class="{{ active(['admin/orderReport/orderPayments','admin/orderReportPrint/orderPayments']) }}">
                                                                    <a href="{{ url('admin/orderReport/orderPayments') }}">
                                                                        <i class="fa fa-money"></i>
                                                                        <span class="title">Payments</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('reportProductPurchased'))
                                                                <li  class="{{ active(['admin/orderReport/productPurchased','admin/orderReportPrint/productPurchased']) }}">
                                                                    <a href="{{ url('admin/orderReport/productPurchased') }}">
                                                                        <i class="entypo-basket"></i>
                                                                        <span class="title">Product Purchased </span>
                                                                    </a>
                                                                </li>
                                                                @endif

                                                            </ul>
                                                        </li>
                                                        @endif

                                                        @if (Auth::user()->hasRolePermission('notifications'))
                                                        <li class="has-sub {{ active(['notifications.*','vendorNotifications.*','trainerNotifications.*'], 'opened') }}">
                                                            <a href="javascript:void(0);">
                                                                <i class="entypo-bell"></i>
                                                                <span class="title">Notifications</span>
                                                            </a>
                                                            <ul>                   
                                                                <li class="{{ active('notifications.*') }}">
                                                                    <a href="{{ url('admin/notifications') }}" >
                                                                        <i class="entypo-bell"></i>
                                                                        <span class="title">General</span>
                                                                    </a>
                                                                </li>
                                                                <li  class="{{ active('admin/vendorNotifications') }}">
                                                                    <a href="{{ url('admin/vendorNotifications') }}">
                                                                        <i class="entypo-bell"></i>
                                                                        <span class="title">Vendors</span>
                                                                    </a>
                                                                </li>
                                                                <li  class="{{ active('admin/trainerNotifications') }}">
                                                                    <a href="{{ url('admin/trainerNotifications') }}">
                                                                        <i class="entypo-bell"></i>
                                                                        <span class="title">Trainers</span>
                                                                    </a>
                                                                </li>

                                                            </ul>
                                                        </li>
                                                        @endif

                                                        @if (Auth::user()->hasRolePermission('users') || Auth::user()->hasRolePermission('permissions'))
                                                        <li class="has-sub {{ active(['users.*','permissions.*'], 'opened') }}">
                                                            <a href="javascript:void(0);">
                                                                <i class="entypo-layout"></i>
                                                                <span class="title">Administrators</span>
                                                            </a>
                                                            <ul>
                                                                @if (Auth::user()->hasRolePermission('users'))
                                                                <li class="{{ active('users.*') }}">

                                                                    <a href="{{ url('admin/users') }}">
                                                                        <i class="entypo-user"></i>
                                                                        <span class="title">Users</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('permissions'))
                                                                <li class="{{ active('permissions.*') }}">
                                                                    <a href="{{ url('admin/permissions') }}">
                                                                        <i class="entypo-users"></i>
                                                                        <span class="title">Users Group </span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                            </ul>
                                                        </li>
                                                        @endif
                                                        @if (Auth::user()->hasRolePermission('activities') || Auth::user()->hasRolePermission('modules') || Auth::user()->hasRolePermission('areas') || Auth::user()->hasRolePermission('amenities') || Auth::user()->hasRolePermission('paymentModes'))
                                                        <li class="has-sub {{ active(['activities.*','modules.*','areas.*','amenities.*','paymentModes.*'], 'opened') }}">
                                                            <a href="javascript:void(0);">
                                                                <i class="entypo-newspaper"></i>
                                                                <span class="title">Master Records</span>
                                                            </a>
                                                            <ul>
                                                                @if (Auth::user()->hasRolePermission('activities'))
                                                                <li class="{{ active('activities.*') }}">
                                                                    <a href="{{ url('admin/activities') }}">
                                                                        <i class="entypo-compass"></i>
                                                                        <span class="title">Activities</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('modules'))
                                                                <li class="{{ active('modules.*') }}">
                                                                    <a href="{{ url('admin/modules') }}">
                                                                        <i class="entypo-flow-cascade"></i>
                                                                        <span class="title">Modules</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('areas'))
                                                                <li class="{{ active('areas.*') }}">
                                                                    <a href="{{ url('admin/areas') }}">
                                                                        <i class="entypo-chart-area"></i>
                                                                        <span class="title">Areas</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('amenities'))
                                                                <li class="{{ active('amenities.*') }}">
                                                                    <a href="{{ url('admin/amenities') }}">
                                                                        <i class="entypo-magnet"></i>
                                                                        <span class="title">Amenities</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @if (Auth::user()->hasRolePermission('paymentModes'))
                                                                <li class="{{ active('paymentModes.*') }}">
                                                                    <a href="{{ url('admin/paymentModes') }}">
                                                                        <i class="fa fa-money"></i>
                                                                        <span class="title">Payment Mode</span>
                                                                    </a>
                                                                </li>
                                                                @endif
                                                            </ul>
                                                        </li>
                                                        @endif
                                                        @if (Auth::user()->hasRolePermission('cmsPages'))
                                                        <li class="{{ active('cmsPages.*') }}">
                                                            <a href="{{ url('admin/cmsPages') }}" >
                                                                <i class="entypo-publish"></i>
                                                                <span class="title">CMS Pages</span>
                                                            </a>
                                                        </li>
                                                        @endif 
                                                        @if (Auth::user()->hasRolePermission('faq'))
                                                        <li class="{{ active('faq.*') }}">
                                                            <a href="{{ url('admin/faq') }}" >
                                                                <i class="entypo-book-open"></i>
                                                                <span class="title">FAQ</span>
                                                            </a>
                                                        </li>
                                                        @endif 
                                                        @if (Auth::user()->hasRolePermission('sponsoredAds'))
                                                        <li class="{{ active('sponsoredAds.*') }}">
                                                            <a href="{{ url('admin/sponsoredAds') }}" >
                                                                <i class="entypo-picture"></i>
                                                                <span class="title">Sponsored Ads</span>
                                                            </a>
                                                        </li>
                                                        @endif 
                                                        @if (Auth::user()->hasRolePermission('coupons'))
                                                        <li class="{{ active('coupons.*') }}">
                                                            <a href="{{ url('admin/coupons') }}" >
                                                                <i class="entypo-cc-share"></i>
                                                                <span class="title">Coupons</span>
                                                            </a>
                                                        </li>
                                                        @endif 
                                                        @if (Auth::user()->hasRolePermission('contactus'))
                                                        <li class="{{ active('contactus.*') }}">
                                                            <a href="{{ url('admin/contactus') }}" >
                                                                <i class="entypo-phone"></i>
                                                                <span class="title">Contact Entries</span>
                                                            </a>
                                                        </li>
                                                        @endif 

                                                        @if (Auth::user()->hasRolePermission('logActivity'))
                                                        <li class="has-sub {{ active(['admin/logActivity','admin/vendorLogActivity','admin/TrainerLogActivity'], 'opened') }}">
                                                            <a href="javascript:void(0);">
                                                                <i class="entypo-clock"></i>
                                                                <span class="title">Log Activities</span>
                                                            </a>
                                                            <ul>                   
                                                                <li  class="{{ active('admin/logActivity') }}">
                                                                    <a href="{{ url('admin/logActivity') }}">
                                                                        <i class="entypo-clock"></i>
                                                                        <span class="title">Admin Users</span>
                                                                    </a>
                                                                </li>
                                                                <li  class="{{ active('admin/vendorLogActivity') }}">
                                                                    <a href="{{ url('admin/vendorLogActivity') }}">
                                                                        <i class="entypo-clock"></i>
                                                                        <span class="title">Vendors</span>
                                                                    </a>
                                                                </li>
                                                                <li  class="{{ active('admin/trainerLogActivity') }}">
                                                                    <a href="{{ url('admin/trainerLogActivity') }}">
                                                                        <i class="entypo-clock"></i>
                                                                        <span class="title">Trainers</span>
                                                                    </a>
                                                                </li>

                                                            </ul>
                                                        </li>
                                                        @endif

                                                        @if (Auth::user()->hasRolePermission('contractExpired'))
                                                        <li class="{{ active('expiredContracts.*') }}">
                                                            <a href="{{ url('admin/expiredContracts') }}">
                                                                <i class="entypo-clock"></i>
                                                                <span class="title">Expired Contracts</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (Auth::user()->hasRolePermission('backup'))
                                                        <li  class="{{ active('admin/backup') }}">
                                                            <a href="{{ url('admin/backup') }}">
                                                                <i class="entypo-upload"></i>
                                                                <span class="title">Backups</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (Auth::user()->hasRolePermission('languageManagement'))
                                                        <li class="{{ active('admin/languageManagement') }}">
                                                            <a href="{{ url('admin/languageManagement') }}">
                                                                <i class="entypo-globe"></i>
                                                                <span class="title">Language Management</span>
                                                            </a>
                                                        </li>
                                                        @endif 

                                                        </ul>
                                                        </li>


                                                        </ul>

                                                        </div>

                                                        </div>