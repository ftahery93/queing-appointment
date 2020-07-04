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
         
            @if (Auth::user()->hasRolePermission('ministryUsers'))
            <li  class="{{ active('ministryUsers.*') }}">
                <a href="{{ url('admin/ministryUsers') }}">
                    <i class="entypo-user"></i>
                    <span class="title">Ministry Users</span>
                    <?php /* ?><span class="badge orange">{{ $trainer_records_count }}</span><?php */ ?>
                </a>
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
                                                      
                                                       
                                                        </ul>
                                                        </li>


                                                        </ul>

                                                        </div>

                                                        </div>