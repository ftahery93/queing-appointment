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
                    
            
            @if (Auth::guard('trainer')->user()->branch_id==0)
            <li  class="{{ active('trainer/branches') }}">
                <a href="{{ url('trainer/branches') }}">
                    <i class="entypo-bag"></i>
                    <span class="title">Branches</span>
                </a>
            </li>
            <li class="{{ active('trainer/users.*') }}">

                <a href="{{ url('trainer/users') }}">
                    <i class="entypo-user"></i>
                    <span class="title">Workers</span>
                </a>
            </li>
            @endif
            <li class="{{ active('trainer/services.*') }}">

                <a href="{{ url('trainer/services') }}">
                    <i class="entypo-tools"></i>
                    <span class="title">Services</span>
                </a>
            </li>
            <li class="{{ active('trainer/appointments.*') }}">

                <a href="{{ url('trainer/appointments') }}">
                    <i class="entypo-clock"></i>
                    <span class="title">Appointments</span>
                </a>
            </li>
            <li class="{{ active('trainer/queues.*') }}">

                <a href="{{ url('trainer/queues') }}">
                    <i class="entypo-clock"></i>
                    <span class="title">Queues</span>
                </a>
            </li>
                               
                </ul>
            </li>

        </ul>
        </li>


        </ul>

    </div>

</div>