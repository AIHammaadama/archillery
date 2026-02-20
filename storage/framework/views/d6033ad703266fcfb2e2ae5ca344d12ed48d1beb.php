<!--**********************************
            Nav header start
        ***********************************-->
<div class="nav-header">
    <a href="<?php echo e(url('/')); ?>" class="brand-logo">
        <img class="logo-abbr" style="width: 400px; margin-right:-20px; margin-top: 5px;"
            src="<?php echo e(asset('/images/logo.png')); ?>" alt="">
        <img class="logo-compact" src="<?php echo e(asset('/images/logo-text.png')); ?>" alt="">
        <img class="brand-title" src="<?php echo e(asset('/images/logo-text.png')); ?>" alt="">
    </a>
</div>
<!--**********************************
            Nav header end
        ***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left"></div>
                <ul class="navbar-nav header-right">
                    <?php if(auth()->guard()->guest()): ?>
                    <?php if(Route::has('login')): ?>
                    <li class="nav-item">
                        <a class="btn light btn-secondary" href="<?php echo e(route('login')); ?>"><?php echo e(__('Login')); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php else: ?>
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:;" role="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <img <?php if(!empty(Auth::user()->photo)): ?> src="<?php echo e(url('storage/'.Auth::user()->photo)); ?>" <?php else: ?>
                            src="https://www.pngfind.com/pngs/m/610-6104451_image-placeholder-png-user-profile-placeholder-image-png.png"
                            <?php endif; ?> width="20" alt="avatar">
                            <div class="header-info">
                                <span>Hello, <strong><?php echo e(Auth::user()->firstname); ?></strong></span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="<?php echo e(route('dashboard')); ?>" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-secondary"
                                    width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ml-2">Dashboard </span>
                            </a>
                            <a class="dropdown-item ai-icon" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ml-2"><?php echo e(__('Logout')); ?> </span>
                            </a>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                <?php echo csrf_field(); ?>
                            </form>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/partials/navbar.blade.php ENDPATH**/ ?>