<?php $__env->startSection('title', 'Notifications | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Notifications</h4>
                    <p class="mb-0">View and manage your notifications</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
                </ol>
            </div>
        </div>

        <!-- Notification list -->
        <div class="row" x-data="notifications()">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Notifications</h4>
                        <button type="button" class="btn btn-sm btn-primary" @click="markAllAsRead()"
                            x-show="unreadCount > 0">
                            <i class="bi bi-check-all"></i> Mark all as read
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if($notifications->count() > 0): ?>
                        <div class="notification-list">
                            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div
                                class="notification-item p-3 border-bottom <?php echo e(is_null($notification->read_at) ? 'bg-light' : ''); ?>">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <div class="notification-icon">
                                            <?php if(str_contains($notification->type, 'Request')): ?>
                                            <i class="bi bi-file-earmark-text text-primary fs-3"></i>
                                            <?php elseif(str_contains($notification->type, 'Project')): ?>
                                            <i class="bi bi-folder text-success fs-3"></i>
                                            <?php elseif(str_contains($notification->type, 'Delivery')): ?>
                                            <i class="bi bi-truck text-info fs-3"></i>
                                            <?php elseif(str_contains($notification->type, 'Approval')): ?>
                                            <i class="bi bi-check-circle text-warning fs-3"></i>
                                            <?php else: ?>
                                            <i class="bi bi-bell text-secondary fs-3"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <?php echo e($notification->data['title'] ?? 'Notification'); ?>

                                            <?php if(is_null($notification->read_at)): ?>
                                            <span class="badge badge-danger badge-sm ms-2">New</span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="mb-1 text-muted">
                                            <?php echo e($notification->data['message'] ?? 'No message'); ?>

                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                <?php echo e($notification->created_at->diffForHumans()); ?>

                                            </small>
                                            <div>
                                                <?php if(isset($notification->data['url'])): ?>
                                                <a href="<?php echo e($notification->data['url']); ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                                <?php endif; ?>
                                                <?php if(is_null($notification->read_at)): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    @click="markAsRead('<?php echo e($notification->id); ?>')">
                                                    Mark as read
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <!-- Pagination -->
                        <!-- Pagination -->
                        <?php if($notifications->hasPages()): ?>
                        <div class="mt-4">
                            <?php echo e($notifications->links('pagination::bootstrap-4')); ?>

                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash display-1 text-muted"></i>
                            <h5 class="mt-3">No notifications yet</h5>
                            <p class="text-muted">You don't have any notifications at the moment.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/notifications/index.blade.php ENDPATH**/ ?>