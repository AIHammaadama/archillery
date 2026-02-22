<!DOCTYPE html>
<html lang="en" class="h-100">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>@yield('title')</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/favicon.png') }}">
        <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
    </head>

    <body class="h-100">
        <div class="authincation h-100">
            @include("partials.navbar")
            <div class="container h-100" style="margin-top: -100px;">
                <div class="row justify-content-center h-100 align-items-center">
                    @yield('content')
                </div>
            </div>
        </div>

    </body>

</html>