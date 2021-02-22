<div class="page-header navbar navbar-fixed-top">

    <div class="page-header-inner ">

        <div class="page-logo">

            <a href="{{ route('admin.dashboard.index') }}">

                <p class="logo-default">

                    <img src="{{ asset('theme/images/logo.png') }}" alt="logo" />

                </p>

            </a>

            <div class="menu-toggler sidebar-toggler">

                <span></span>

            </div>

        </div>

        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">

            <span></span>

        </a>

        <div class="top-menu">

            <ul class="nav navbar-nav pull-right">

                <li class="dropdown dropdown-user">

                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">

                        <img alt="" class="img-circle" src="{{ !empty(Auth::user()->profile) ? Auth::user()->profile : '' }}" />

                        <span class="username username-hide-on-mobile"> {{ !empty(Auth::user()->name)? Auth::user()->name: '' }} </span>

                        <i class="fa fa-angle-down"></i>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-default">

                        <li>

                            <a href="{{ route('admin.showProfile') }}">

                                <i class="fa fa-user"></i> My Profile

                            </a>

                        </li>

                        <li class="divider"> </li>

                        <li>

                            <a href="{{ route('distributor.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                                <i class="fa fa-sign-out"></i> Log Out

                            </a>

                            <form id="logout-form" action="{{ route('distributor.logout') }}" method="POST" style="display: none;">

                                {{ csrf_field() }}

                            </form>

                        </li>

                    </ul>

                </li>

            </ul>

        </div>

        <!-- END TOP NAVIGATION MENU -->

    </div>

    <!-- END HEADER INNER -->

</div>
