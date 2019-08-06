<div class="row header_top">

    <!-- Profile Info and Notifications -->
    <div class="col-md-6 col-sm-8 clearfix">

        <ul class="user-info pull-left pull-none-xsm" style="margin-top:2px;">

            <!-- Profile Info -->
            <li class="profile-info dropdown"><!-- add class "pull-right" if you want to place this from right -->

                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ asset('assets/images/user_noimage.png') }}" alt="" class="img-circle" width="44" />
                    {{ $userInfo->username }}
                </a>

                <ul class="dropdown-menu">

                    <!-- Reverse Caret -->
                    <li class="caret"></li>

                    <!-- Profile sub-links -->
                    <li>
                        <a href="{{ url('admin/user/profile') }}">
                            <i class="entypo-user"></i>
                            Edit Profile
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/user/changepassword') }}">
                            <i class="entypo-lock"></i>
                            Change Password
                        </a>
                    </li>


                </ul>
            </li>

        </ul>


    </div>


    <!-- Raw Links -->
    <div class="col-md-6 col-sm-4 clearfix hidden-xs">

        <ul class="list-inline links-list pull-right" style="position:relative;">

             <?php /* ?>
            <li  style="margin-right:20px;">
                <a href="{{ url('/admin/pendingVendorClasses') }}" >
                    <i class="entypo-back-in-time"></i>
                    Pending Classes

                    <span @if($pendingClasses!=0) class="badge badge-success chat-notifications-badge animated bounce" @else class="badge badge-success chat-notifications-badge" @endif  style="position:absolute;">{{ $pendingClasses }}</span>
                </a>
            </li>
              <?php */ ?>
            
            <li class="sep"></li>
            <li>
                <a href="{{ url('/admin/logout') }}">
                    Log Out <i class="entypo-logout right"></i>
                </a>
            </li>
        </ul>

    </div>

</div>

<div style="width:100%;margin-bottom:20px;"></div>

<ol class="breadcrumb bc-3" >
    @if (Auth::user()->hasRolePermission('dashboard'))
    <li>
        <a href="{{ url('/admin/dashboard') }}"><i class="entypo-gauge"></i>Dashboard</a>
    </li>
    @endif
    @yield('breadcrumb')
</ol>

<h2>@yield('pageheading')</h2>
<br />
   