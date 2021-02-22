<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    @php
        $asset_v = '1.3';
    @endphp
    <!--<![endif]-->
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <head>
        <meta charset="utf-8" />
        <title>{{ config('app.name') }}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{ asset('theme/images/favicon.png') }}" />
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/font-awesome.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/bootstrap.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/components.min.css') . '?v=' . $asset_v }}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{ asset('theme/css/plugins.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/layout.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/darkblue.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" id="style_color" />
        <link href="{{ asset('theme/css/bootstrap-switch.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/toastr.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/bootstrap-datepicker.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/bootstrap-timepicker.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/select2.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/select2-bootstrap.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/datatables.min.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />


        @stack('page_css')
        <link href="{{ asset('theme/css/custom.css') . '?v=' . $asset_v }}" rel="stylesheet" type="text/css" />
    </head>

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            @include('distributor.shared.header')
            <div class="clearfix"> </div>
            <div class="page-container">
                @include('distributor.shared.sidebar')
                <div class="page-content-wrapper">
                    <div class="page-content">
                        @yield('breadcrumb')
                        <h1 class="page-title">
                            {{-- <small>statistics, charts, recent events and reports</small> --}}
                        </h1>
                        @yield('content')
                    </div>
                </div>
            </div>
            @include('distributor.shared.footer')
            @include('flash::message')
        </div>
        <!--[if lt IE 9]>
        <script src="{{ asset('theme/js/respond.min.js') . '?v=' . $asset_v }}"></script>
        <script src="{{ asset('theme/js/excanvas.min.js') . '?v=' . $asset_v }}"></script>
        <script src="{{ asset('theme/js/ie8.fix.min.js') . '?v=' . $asset_v }}"></script>
        <![endif]-->
        <script src="{{ asset('theme/js/jquery.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/bootstrap.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/jquery.slimscroll.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/jquery.blockui.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/js.cookie.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/bootstrap-switch.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/bootbox.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/toastr.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/bootstrap-datepicker.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/bootstrap-timepicker.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/jquery.validate.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/additional-methods.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/select2.full.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/datatables.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        @stack('page_js')
        <script src="{{ asset('theme/js/app.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/layout.min.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/custom_validations.js') . '?v=' . $asset_v }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/custom.js') . '?v=' . $asset_v }}" type="text/javascript"></script>

        @stack('scripts')
    </body>
</html>
