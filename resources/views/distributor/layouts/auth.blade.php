<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <title>{{ config('app.name') }} | Authentication</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{ asset('theme/images/favicon.png') }}" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{ asset('theme/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('theme/css/login.min.css') }}" rel="stylesheet" type="text/css" />
    </head>
    <body class="login">
        <div class="logo">
            <a href="{{ url('admin/login') }}">
                <img src="{{ asset('theme/images/logo.png') }}" alt="Logo" />
            </a>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="copyright"> {{ date('Y') }} &copy; {{ config('app.name') }} | All rights reserved </div>
        <!--[if lt IE 9]>
        <script src="{{ asset('theme/js/respond.min.js') }}"></script>
        <script src="{{ asset('theme/js/excanvas.min.js') }}"></script> 
        <script src="{{ asset('theme/js/ie8.fix.min.js') }}"></script> 
        <![endif]-->
        <script src="{{ asset('theme/js/jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/js.cookie.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/jquery.blockui.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/jquery.validate.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/additional-methods.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/jquery.backstretch.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/app.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/custom_validations.js') }}" type="text/javascript"></script>
        <script src="{{ asset('theme/js/login.js') }}" type="text/javascript"></script>
    </body>
</html>