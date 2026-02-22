<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title')</title>
    <script>
        (function() {
            const theme = localStorage.getItem("theme") || "light";
            document.documentElement.setAttribute("data-theme", theme);
            document.body.classList.add(theme + "-mode");
        })();
    </script>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/favicon.png') }}">
    <link href="{{ asset('/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" }}tstrap-select.min.css"
        rel="stylesheet">
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom.css') }}" rel="stylesheet">
    <link href="https://cdn.lineicons.com/2.0/LineIcons.css" rel="stylesheet">
</head>

<body class="h-100" style="background-color: #f6ecfaff;">
    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="{!! url('/'); !!}" class="brand-logo">
                @if(!empty($logo))
                <img class="logo-abbr" src="{{ asset($logo) }}" alt="">
                @else
                <img class="logo-abbr" src="{{ asset('images/logo.png') }}" alt="">
                @endif
                @if(!empty($logoText))
                <img class="logo-compact" src="{{ asset($logoText) }}" alt="">
                <img class="brand-title" src="{{ asset($logoText) }}" alt="">
                @else
                <img class="logo-compact" src="{{ asset('images/logo-text.png') }}" alt="">
                <img class="brand-title" src="{{ asset('images/logo-text.png') }}" alt="">
                @endif
            </a>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->

        @include('partials.navbar')
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="authincation h-100">
            <div class="row justify-content-center h-100 align-items-center">
                @yield('content')
            </div>
        </div>

        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->



        <!--**********************************
            Footer end
        ***********************************-->

        <!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->


    <!-- Dashboard 1 -->
    <script src="{{ asset('/js/dashboard/doctor-details.js') }}"></script>

    <script src="{{ asset('/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('/js/custom.min.js') }}"></script>
    <script src="{{ asset('/js/deznav-init.js') }}"></script>
    <script src="{{ asset('/vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('/vendor/owl-carousel/owl.carousel.js') }}"></script>

    <!-- Chart Chartist plugin files -->
    <script src="{{ asset('/vendor/chartist/js/chartist.min.js') }}"></script>
    <script src="{{ asset('/vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js') }}"></script>

    <!-- Dashboard 1 -->
    <script src="{{ asset('/js/dashboard/doctor-details.js') }}"></script>
    <script>
        function assignedDoctor() {

            /*  testimonial one function by = owl.carousel.js */
            jQuery('.assigned-doctor2').owlCarousel({
                loop: true,
                margin: 30,
                nav: true,
                autoplaySpeed: 3000,
                navSpeed: 3000,
                paginationSpeed: 3000,
                slideSpeed: 3000,
                smartSpeed: 3000,
                autoplay: true,
                dots: false,
                navText: ['<i class="fa fa-caret-left"></i>', '<i class="fa fa-caret-right"></i>'],
                responsive: {
                    0: {
                        items: 1
                    },

                    480: {
                        items: 1
                    },

                    991: {
                        items: 2
                    },
                    1680: {
                        items: 2
                    }
                }
            })
        }

        jQuery(window).on('load', function() {
            setTimeout(function() {
                assignedDoctor();
            }, 1000);
        });
    </script>
</body>

</html>