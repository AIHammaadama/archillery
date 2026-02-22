<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php if(auth()->guard()->check()): ?>
    <meta name="user-id" content="<?php echo e(auth()->id()); ?>">
    <?php endif; ?>
    <meta name="theme-color" content="#ffffff">
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('/images/favicon.png')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('/vendor/select2/css/select2.min.css')); ?>">
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">


    <!-- Vite Assets -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/sass/app.scss', 'resources/js/app.js']); ?>

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

<body x-data="{ theme: '' }" x-init="theme = $store.theme || 'light'">

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
        Notification Toasts Container
    ***********************************-->
    <div x-data="notifications()" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" :class="getToastClass(toast.type)"
                class="toast show mb-2 shadow-lg" role="alert" style="min-width: 300px;">
                <div class="toast-header" :class="getToastClass(toast.type)">
                    <span x-html="getToastIcon(toast.type)" class="me-2"></span>
                    <strong class="me-auto text-capitalize" x-text="toast.type"></strong>
                    <button type="button" class="btn-close btn-close-white" @click="hideToast(toast.id)"></button>
                </div>
                <div class="toast-body" x-text="toast.message"></div>
            </div>
        </template>
    </div>

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
        <!--**********************************
            Header start
        ***********************************-->
        <?php echo $__env->make("partials.admin-header", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        <?php echo $__env->make("partials.admin-sidebar", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <?php echo $__env->yieldContent('content'); ?>
        <!--**********************************
            Content body end
        ***********************************-->

        <!--**********************************
            Footer start
        ***********************************-->
        <?php echo $__env->make("partials.footer", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
    <script src="<?php echo e(asset('/vendor/global/global.min.js')); ?>"></script>
    <!-- <script src="<?php echo e(asset('/vendor/bootstrap-select/dist/js/bootstrap-select.min.js')); ?>"></script> -->
    <script src="<?php echo e(asset('/js/custom.min.js')); ?>"></script>
    <script src="<?php echo e(asset('/js/deznav-init.js')); ?>"></script>
    <script src="<?php echo e(asset('/vendor/chart.js/Chart.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('/vendor/owl-carousel/owl.carousel.js')); ?>"></script>
    <script src="<?php echo e(asset('/vendor/select2/js/select2.full.min.js')); ?>"></script>
    <script src="<?php echo e(asset('/js/plugins-init/select2-init.js')); ?>"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Apex Chart -->
    <!--  <script src="<?php echo e(asset('/vendor/apexchart/apexchart.js')); ?>"></script> -->

    <!-- Dashboard 1 -->
    <!--  <script src="<?php echo e(asset('/js/dashboard/dashboard-1.js')); ?>"></script> -->
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

    <!-- Page-specific scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>


</html><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/layouts/admin.blade.php ENDPATH**/ ?>