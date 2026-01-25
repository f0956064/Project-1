<!doctype html>
<html lang="en">
@include('admin.components.head')
<body {!! \Config::get('settings.page_layout') == 'horizontal' ? 'data-topbar="dark" data-layout-size="boxed"' : 'data-sidebar="dark"' !!} data-layout="{{ \Config::get('settings.page_layout') }}">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">
            @include('admin.components.header')
            @include('admin.components.sidemenu')
            @include('admin.components.flash-message')

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box pb-0 d-flex align-items-center justify-content-between">
                                    <div class="page-title-left">
                                        <h4 class="mb-0 font-size-18 animate__animated animate__heartBeat">{{ $title }}</h4>
                                        @if(isset($data) && is_object($data) && method_exists($data, 'currentPage'))
                                            <p class="font-size-12 text-secondary">Showing page <code>{{ $data->currentPage() }}</code> of <code>{{ $data->lastPage() }}</code>.
                                            </p>
                                        @endif
                                    </div>


                                    <div class="page-title-right">
                                        @if(isset($filters) && !empty($filters))
                                            <button data-toggle="tooltip" data-original-title="Filter Record" class="{{ \Config::get('view.buttons.secondary') }} show-panel" data-id="filter-panel">{!! \Config::get('settings.icon_search') !!}</button>
                                        @endif
                                        @if(isset($header_buttons))
                                            @foreach($header_buttons as $button)
                                                {!! $button !!}
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <div class="row">
                            <div class="col-lg-12">
                                @if(isset($noCardView) && $noCardView)
                                    @yield('content')
                                @else
                                    <div class="cards animate__animated  animate__fadeInUp">
                                        <div class="card-body">
                                            @if(isset($filters) && !empty($filters))
                                            <div class="row clearfix" id="filter-panel" style="display: {{ request()->get('filter') ? 'block' : 'none' }};">
                                                <div class="col-lg-12 col-sm-12">
                                                @include('admin.components.filter-form')
                                                </div>
                                            </div>
                                            @endif
                                            @yield('content')

                                        </div>
                                        @yield('card-footer')
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

                @include('admin.components.footer')

            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- JAVASCRIPT -->
        @include('admin.components.scripts')

    </body>
</html>

