<!DOCTYPE html>
<html lang="en" @if (Route::currentRouteName() == 'admin.rtl_layout') dir="rtl" @endif>

<head>
    @include('layouts.simple.head')
    @include('layouts.simple.css')
</head>

@switch(Route::currentRouteName())
    @case('admin.box_layout')
        <body class="box-layout">
        @break

    @case('admin.rtl_layout')
        <body class="rtl">
        @break

    @case('admin.dark_layout')
        <body class="dark-only">
        @break

    @default
        <body>
@endswitch
                <!-- loader starts-->
                <div class="loader-wrapper">
                    <div class="loader">
                        <div class="loader4"></div>
                    </div>
                </div>
                <!-- loader ends-->

                <!-- tap on top starts-->
                <div class="tap-top"><i data-feather="chevrons-up"></i></div>
                <!-- tap on tap ends-->

                <!-- page-wrapper Start-->
                <div class="page-wrapper compact-wrapper" id="pageWrapper">

                    <!-- Page header start -->
                    @include('layouts.simple.header')
                    <!-- Page header end-->

                    <!-- Page Body Start-->
                    <div class="page-body-wrapper">

                        <!-- Page sidebar start-->
                        @include('layouts.simple.sidebar')
                        <!-- Page sidebar end-->

                        @switch(Route::currentRouteName())
                            @case('admin.checkout')
                                <div class="page-body checkout">
                                @break

                            @default
                                <div class="page-body">
                        @endswitch
                                @yield('main_content')
                            </div>

                            <!-- footer start-->
                            @include('layouts.simple.footer')
                            <!-- footer end-->
                        </div>
                    </div>
                    <!-- page-wrapper Ends-->

                    {{-- scripts --}}
                    @include('layouts.simple.script')
                    @include('admin.inc.alerts')
                    {{-- end scripts --}}

            </body>

</html>
