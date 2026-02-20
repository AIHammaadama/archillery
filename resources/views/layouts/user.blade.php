<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title')</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/select2/css/select2.min.css') }}">
    <!-- <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#state').change(function() {
                var state_id = $(this).val();
                $('#lga').html('');
                $.ajax({
                    url: '/profile/lgas/' + state_id,
                    method: 'GET',
                    success: function(res) {
                        $('#lga').html(res)
                    }
                });
            });

            $('#rstate').change(function() {
                var state_id = $(this).val();
                $('#rlga').html('');
                $.ajax({
                    url: '/profile/lgas/' + state_id,
                    method: 'GET',
                    success: function(res) {
                        $('#rlga').html(res)
                    }
                });
            });
        })
    </script>
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
        <!--**********************************
            Header start
        ***********************************-->
        @include("partials.header")
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include("partials.sidebar")
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        @yield('content')
        <!--**********************************
            Content body end
        ***********************************-->

        <!--**********************************
            Footer start
        ***********************************-->
        @include("partials.footer")
        <!--**********************************
            Footer end
        ***********************************-->
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('/vendor/global/global.min.js') }}"></script>
    <!-- <script src="{{ asset('/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script> -->
    <script src="{{ asset('/js/custom.min.js') }}"></script>
    <script src="{{ asset('/js/deznav-init.js') }}"></script>
    <script src="{{ asset('/vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('/vendor/owl-carousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('/js/plugins-init/select2-init.js') }}"></script>

    <!-- Apex Chart -->
    <!--  <script src="{{ asset('/vendor/apexchart/apexchart.js') }}"></script> -->

    <!-- Dashboard 1 -->
    <!--  <script src="{{ asset('/js/dashboard/dashboard-1.js') }}"></script> -->
    <script>
        function assignedDoctor() {

            /*  testimonial one function by = owl.carousel.js */
            jQuery('.assigned-doctor').owlCarousel({
                loop: false,
                margin: 30,
                nav: true,
                autoplaySpeed: 3000,
                navSpeed: 3000,
                paginationSpeed: 3000,
                slideSpeed: 3000,
                smartSpeed: 3000,
                autoplay: false,
                dots: false,
                navText: ['<i class="fa fa-caret-left"></i>', '<i class="fa fa-caret-right"></i>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    576: {
                        items: 2
                    },
                    767: {
                        items: 3
                    },
                    991: {
                        items: 2
                    },
                    1200: {
                        items: 3
                    },
                    1600: {
                        items: 5
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