<?php $__env->startSection('title', 'Deliveries - ' . $request->request_number . ' | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Deliveries</h4>
                    <p class="mb-0"><?php echo e($request->request_number); ?></p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('requests.index')); ?>">Requests</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?php echo e(route('requests.show', $request)); ?>"><?php echo e($request->request_number); ?></a></li>
                    <li class="breadcrumb-item active">Deliveries</li>
                </ol>
            </div>
        </div>

        <!-- Alerts -->
        <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Delivery Stats -->
        <div class="row mb-3">
            <div class="col-lg-3 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2">Total Items</p>
                                <h4 class="mb-0 text-primary"><?php echo e($stats['total_items']); ?></h4>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="bi bi-box-seam text-primary fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2">Fully Delivered</p>
                                <h4 class="mb-0 text-success"><?php echo e($stats['items_fully_delivered']); ?></h4>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2">Partially Delivered</p>
                                <h4 class="mb-0 text-warning"><?php echo e($stats['items_partially_delivered']); ?></h4>
                            </div>
                            <div class="icon-box bg-warning-light">
                                <i class="bi bi-hourglass-split text-warning fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card card-accent card-accent-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2">Overall Progress</p>
                                <h4 class="mb-0 text-info"><?php echo e($stats['overall_progress']); ?>%</h4>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="bi bi-graph-up text-info fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deliveries by Item -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Delivery Status by Item</h4>
                        <?php if(auth()->user()->hasPermission('record-deliveries') &&
                        in_array($request->status->value, ['approved', 'partially_delivered'])): ?>
                        <a href="<?php echo e(route('deliveries.create', $request)); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Record Delivery
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $totalDelivered = $item->deliveries()
                        ->whereIn('verification_status', ['accepted', 'partial'])
                        ->sum('quantity_delivered');
                        $progress = $item->quantity > 0 ? min(100, ($totalDelivered / $item->quantity) * 100) : 0;
                        $remaining = $item->quantity - $totalDelivered;
                        ?>

                        <div class="card mb-3 border">
                            <div class="card-header bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><?php echo e($item->material->name); ?></h6>
                                        <small class="text-muted"><?php echo e($item->material->code); ?></small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted d-block">Vendor</label>
                                        <?php echo e($item->vendor ? $item->vendor->name : 'N/A'); ?>

                                    </div>
                                    <div class="col-md-3 text-end">
                                        <label class="small text-muted d-block">Progress</label>
                                        <span class="fw-bold <?php echo e($progress >= 100 ? 'text-success' : 'text-warning'); ?>">
                                            <?php echo e(number_format($totalDelivered, 2)); ?> /
                                            <?php echo e(number_format($item->quantity, 2)); ?>

                                            <?php echo e($item->material->unit_of_measurement); ?>

                                        </span>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar <?php echo e($progress >= 100 ? 'bg-success' : 'bg-warning'); ?>"
                                        role="progressbar" style="width: <?php echo e($progress); ?>%"
                                        aria-valuenow="<?php echo e($progress); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <?php if($item->deliveries->count() > 0): ?>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Delivery #</th>
                                            <th>Date</th>
                                            <th>Quantity</th>
                                            <th>Received By</th>
                                            <th>Status</th>
                                            <th>SM Feedback</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $item->deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(route('deliveries.show', $delivery)); ?>"
                                                    class="fw-bold text-primary">
                                                    <?php echo e($delivery->delivery_number); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($delivery->delivery_date->format('M d, Y')); ?></td>
                                            <td><?php echo e(number_format($delivery->quantity_delivered, 2)); ?>

                                                <?php echo e($item->material->unit_of_measurement); ?>

                                            </td>
                                            <td><?php echo e($delivery->receivedBy ? $delivery->receivedBy->firstname . ' ' . $delivery->receivedBy->lastname : 'N/A'); ?>

                                            </td>
                                            <td>
                                                <?php if($delivery->verification_status === 'accepted'): ?>
                                                <span class="badge badge-success">Accepted</span>
                                                <?php elseif($delivery->verification_status === 'rejected'): ?>
                                                <span class="badge badge-danger">Rejected</span>
                                                <?php elseif($delivery->verification_status === 'partial'): ?>
                                                <span class="badge badge-warning">Partial</span>
                                                <?php else: ?>
                                                <span class="badge badge-secondary">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($delivery->siteManagerCanUpdate()): ?>
                                                <?php if($delivery->site_manager_status === 'received'): ?>
                                                <span class="badge badge-success" title="Good Condition">
                                                    <i class="bi bi-check-circle"></i>
                                                </span>
                                                <?php elseif($delivery->site_manager_status === 'issues_noted'): ?>
                                                <span class="badge badge-warning" title="Issues Noted">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                </span>
                                                <?php elseif($delivery->site_manager_status === 'completed'): ?>
                                                <span class="badge badge-primary" title="Completed">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </span>
                                                <?php else: ?>
                                                <span class="badge badge-light text-muted" title="Pending Feedback">
                                                    <i class="bi bi-dash-circle"></i>
                                                </span>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('deliveries.show', $delivery)); ?>"
                                                    class="btn btn-sm btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="card-body text-center py-3">
                                <small class="text-muted">No deliveries recorded yet</small>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.bg-primary-light {
    background-color: rgba(124, 58, 237, 0.1);
}

.bg-success-light {
    background-color: rgba(16, 185, 129, 0.1);
}

.bg-warning-light {
    background-color: rgba(245, 158, 11, 0.1);
}

.bg-info-light {
    background-color: rgba(6, 182, 212, 0.1);
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/deliveries/index.blade.php ENDPATH**/ ?>