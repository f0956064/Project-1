@php ($adminMenu = \App\Models\MasterMenu::getMenu() )
@php ($className  =  \App\Helpers\Helper::getController())

<!-- ========== Left Sidebar Start ========== -->
@if(\Config::get('settings.page_layout') == 'horizontal')
    <div class="topnav">
        <div class="container-fluid">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                <div class="collapse navbar-collapse" id="topnav-menu-content">
                    <ul class="navbar-nav">

                        <li class="{{ ($className == 'DashboardController') ? 'active' : '' }} nav-item"><a href="{{ route('admin.home') }}" id="horizontal-menu-home" class="nav-link waves-effect"><i class="bx bxs-dashboard mr-2"></i> <span>Dashboard</span></a></li>

                        @foreach($adminMenu as $key => $menu)
                            @if($menu['child'])
                            <li class="nav-item dropdown {{ (($className == $menu['class']) ? ' active' : '') }}">
                                <a class="nav-link dropdown-toggle arrow-none" href="javascript:void(0);" id="horizontal-topnav-{{ $menu['id'] }}" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="{{ $menu['icon'] }} mr-2"></i>
                                    <span key="t-ui-elements"> {{ $menu['menu'] }}</span>
                                    <div class="arrow-down"></div>
                                </a>

                                <div class="dropdown-menu" aria-labelledby="horizontal-topnav-{{ $menu['id'] }}">
                                    @foreach($menu['child'] as $k => $childMenu)
                                        @if(Route::has($childMenu['url']))
                                            <a href="{{ route($childMenu['url'], $childMenu['query_params']) }}" class="dropdown-item {{ ($className == $childMenu['class']) ? 'active' : '' }}" key="t-{{ $childMenu['id'] }}">{{ $childMenu['menu'] }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </li>
                            @else
                                @if(Route::has($menu['url']))
                                    <li class="{{ ($className == 'DashboardController') ? 'active' : '' }} nav-item"><a href="{{ route($menu['url'], $menu['query_params']) }}" id="horizontal-menu-{{ $menu['id'] }}" class="nav-link waves-effect"><i class="{{ $menu['icon'] }} mr-2"></i> <span>{{ $menu['menu'] }}</span></a></li>
                                @endif
                            @endif
                        @endforeach
                    </ul>
                </div>
            </nav>
        </div>
    </div>

@else
    <div class="vertical-menu">
        <div data-simplebar class="h-100">

            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <!-- Left Menu Start -->
                <ul class="metismenu list-unstyled" id="side-menu">
                    {{--<li class="menu-title" key="t-menu">Menu</li>--}}

                    <li class="{{ ($className == 'DashboardController') ? 'active' : '' }}"><a href="{{ route('admin.home') }}" id="vertical-menu-home" class="waves-effect"><i class="bx bxs-dashboard"></i> <span>Dashboard</span></a></li>
                    @foreach($adminMenu as $key => $menu)
                    <li class="{{ ($className == $menu['class']) ? 'active' : '' }}">
                        @if($menu['child'])
                        <a href="javascript:void(0);" class="menu-toggle waves-effect has-arrow">
                            <i class="{{ $menu['icon'] }}"></i>
                            <span>{{ $menu['menu'] }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            @foreach($menu['child'] as $k => $childMenu)
                                @if(Route::has($childMenu['url']))
                                    <li class="{{ ($className == $childMenu['class']) ? 'active' : '' }}">
                                        <a href="{{route($childMenu['url'], $childMenu['query_params'])}}" id="vertical-menu-{{ $childMenu['id'] }}" class="has-link"><span>{{ $childMenu['menu'] }}</span></a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        @else
                            @if(Route::has($menu['url']))
                                <a href="{{ route($menu['url'], $menu['query_params']) }}" id="vertical-menu-{{ $menu['id'] }}" class="has-link"><i class="{{ $menu['icon'] }}"></i> <span>{{ $menu['menu'] }}</span></a>
                            @endif
                        @endif
                    </li>
                    @endforeach

                </ul>
            </div>
            <!-- Sidebar -->
        </div>
    </div>
@endif
<!-- Left Sidebar End -->