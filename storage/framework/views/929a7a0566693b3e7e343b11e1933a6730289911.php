<!--**********************************
            Nav header start
        ***********************************-->
<div class="nav-header">
    <a href="<?php echo e(url('/')); ?>" class="brand-logo">
        <!-- Light mode logo (dark logo image) -->
        <img class="logo-abbr" src="<?php echo e(asset('/images/logo.png')); ?>" alt="">
        <img class="brand-title logo-light" src="<?php echo e(asset('/images/logo-text-dark.png')); ?>" alt="">

        <!-- Dark mode logo (light logo image) -->
        <img class="brand-title logo-dark" src="<?php echo e(asset('/images/logo-text-light.png')); ?>" alt="">
    </a>

    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>
<!--**********************************
            Nav header end
        ***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <!-- <div class="font-sans">
                        <h2><?php echo $__env->yieldContent('title'); ?></h2>
                    </div> -->
                </div>
                <ul class="navbar-nav header-right font-sans">

                    <?php if(auth()->guard()->guest()): ?>
                    <?php if(Route::has('login')): ?>
                    <li class="nav-item">
                        <a class="btn light btn-secondary" href="<?php echo e(route('login')); ?>"><?php echo e(__('Login')); ?></a>
                    </li>
                    <?php endif; ?>

                    <?php if(Route::has('register')): ?>
                    <li class="nav-item">
                        <a class="btn  btn-secondary mr-4" href="<?php echo e(route('register')); ?>"><?php echo e(__('Register')); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php else: ?>
                    <!-- Theme Toggle -->
                    <li class="nav-item" x-data="themeToggle()">
                        <a class="nav-link" href="javascript:void(0)" @click="toggle()" :title="label">
                            <i :class="icon" class="fs-4"></i>
                        </a>
                    </li>

                    <!-- Notification Bell -->
                    <li class="nav-item dropdown notification_dropdown" x-data="notifications()"> <a
                            class="nav-link bell bell-link position-relative d-flex align-items-center"
                            href="javascript:void(0)" @click="toggleDropdown()" :class="{ 'show': dropdownOpen }"> <i
                                class="bi bi-bell-fill fs-4"></i> <span x-show="unreadCount > 0" x-text="unreadCount"
                                class="badge bg-danger badge-sm position-absolute top-0 start-100 translate-middle"
                                style="font-size: 0.65rem; min-width: 18px;"> </span> </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg" :class="{ 'show': dropdownOpen }"
                            x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            style="width: 350px; max-height: 500px; overflow-y: auto; position: absolute; right: 0; left: auto;">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <h6 class="mb-0">Notifications</h6> <a href="javascript:void(0)"
                                    @click="markAllAsRead()" x-show="unreadCount > 0" class="btn btn-secondary btn-sm"
                                    style="cursor: pointer;"> Mark all read </a>
                            </div>
                            <div x-show="loading" class="text-center p-3">
                                <div class="spinner-border spinner-border-sm" role="status"> <span
                                        class="visually-hidden">Loading...</span> </div>
                            </div>
                            <div x-show="!loading && notifications.length === 0" class="text-center p-4 text-muted"> <i
                                    class="bi bi-bell-slash fs-1"></i>
                                <p class="mb-0 mt-2">No notifications</p>
                            </div>
                            <div x-show="!loading && notifications.length > 0"> <template
                                    x-for="notification in notifications.slice(0, 10)" :key="notification.id"> <a
                                        :href="notification.data?.url || '#'" @click="markAsRead(notification.id)"
                                        class="dropdown-item py-3" :class="{ 'bg-light': !notification.read_at }">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3"> <i
                                                    :class="'bi ' + getNotificationIcon(notification) + ' fs-4'"
                                                    class="text-primary"></i> </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 small fw-bold" x-text="notification.data?.title"></p>
                                                <p class="mb-1 small text-muted" x-text="notification.data?.message">
                                                </p> <small class="text-muted"
                                                    x-text="timeAgo(notification.created_at)"></small>
                                            </div>
                                        </div>
                                    </a> </template> </div>
                            <div class="dropdown-divider"></div> <a href="<?php echo e(route('notifications')); ?>"
                                class="dropdown-item text-center text-primary py-2"> View all notifications </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown header-profile"> <a class="nav-link" href="javascript:;" role="button"
                            data-toggle="dropdown" aria-expanded="false"> <img id="navPreview"
                                <?php if(!empty(Auth::user()->photo)): ?>
                            src="<?php echo e(asset('storage/profile/'.Auth::user()->photo)); ?>" <?php else: ?>
                            src="https://www.pngfind.com/pngs/m/610-6104451_image-placeholder-png-user-profile-placeholder-image-png.png"
                            <?php endif; ?> width="20" alt="avatar"> <div class="header-info"> <span>
                                    <?php echo e(Auth::user()->firstname.' '.Auth::user()->lastname); ?></span>
                            </div> </a>
                        <div class="dropdown-menu dropdown-menu-right"> <a href="<?php echo e(route('profile')); ?>"
                                class="dropdown-item ai-icon"> <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg"
                                    class="text-secondary" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg> <span class="ml-2">Profile </span> </a> <a class="dropdown-item ai-icon"
                                href="<?php echo e(route('logout')); ?>"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <svg
                                    id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg> <span class="ml-2"><?php echo e(__('Logout')); ?> </span> </a>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"> <?php echo csrf_field(); ?>
                            </form>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div>

<style>
/* Notification Bell and Dropdown Styles */
.notification_dropdown {
    position: relative;
}

.notification_dropdown .dropdown-menu {
    position: absolute !important;
    right: 0 !important;
    left: auto !important;
    top: 100% !important;
    margin-top: 0.5rem;
    width: 350px;
    max-width: 90vw;
    max-height: 500px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1050;
}

/* Prevent bell icon from moving */
.notification_dropdown .nav-link {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

/* Notification item text truncation */
.notification_dropdown .dropdown-item {
    white-space: normal !important;
    word-wrap: break-word !important;
    padding: 0.75rem 1rem !important;
}

.notification_dropdown .dropdown-item .flex-grow-1 {
    min-width: 0;
    overflow: hidden;
}

.notification_dropdown .dropdown-item p {
    margin-bottom: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    word-wrap: break-word;
    word-break: break-word;
}

/* Title: 1 line with ellipsis */
.notification_dropdown .dropdown-item p.fw-bold {
    -webkit-line-clamp: 1;
    line-clamp: 1;
}

/* Message: 2 lines with ellipsis */
.notification_dropdown .dropdown-item p.text-muted {
    -webkit-line-clamp: 2;
    line-clamp: 2;
}

/* Icon container - prevent shrinking */
.notification_dropdown .dropdown-item .me-3 {
    flex-shrink: 0;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .notification_dropdown .dropdown-menu {
        width: 300px !important;
    }
}
</style><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/partials/admin-header.blade.php ENDPATH**/ ?>