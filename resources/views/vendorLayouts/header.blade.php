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
                        <a href="{{ url($configName.'/user/profile') }}">
                            <i class="entypo-user"></i>
                            Edit Profile
                        </a>
                    </li>
                    @if($userInfo->user_role_id==1)
                     <li>
                        <a href="{{ url($configName.'/user/info') }}">
                            <i class="entypo-cog"></i>
                            Settings
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ url($configName.'/user/changepassword') }}">
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

        <ul class="list-inline links-list pull-right">
            <?php /* ?>
         <li  style="margin-right:20px;">
                <a href="{{ url($configM2.'/rejectedClasses') }}" >
                    <i class="entypo-back-in-time"></i>
                    Rejected Classes

                    <span @if($rejectedClasses!=0) class="badge badge-success chat-notifications-badge animated bounce" @else class="badge badge-success chat-notifications-badge" @endif  style="position:absolute;">{{ $rejectedClasses }}</span>
                </a>
            </li>
            <?php */ ?>
           
            <li class="sep"></li>

            <li>
                <a href="{{ url($configName.'/logout') }}">
                    Log Out <i class="entypo-logout right"></i>
                </a>
            </li>
        </ul>

    </div>

</div>
@if (!str_contains($pathURL['path'], 'home'))
<div style="width:100%;margin-bottom:20px;"></div>


<ol class="breadcrumb bc-3" >
   
    <li>
        <a href="{{ url($configName.'/home') }}"><i class="entypo-gauge"></i>Home</a>
    </li>
    
    @yield('breadcrumb')
</ol>
@endif
<h2>@yield('pageheading')</h2>
<br />