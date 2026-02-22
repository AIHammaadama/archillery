<?php $__env->startSection('title', 'Deliveries | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Deliveries Management</h4>
                    <p class="mb-0">Track and record material deliveries</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Deliveries</li>
                </ol>
            </div>
        </div>

        <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row mb-3">
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Pending Delivery</p>
                                <h3 class="mb-0 text-primary"><?php echo e($stats['approved']); ?></h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="bi bi-truck text-primary fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Partially Delivered</p>
                                <h3 class="mb-0 text-warning"><?php echo e($stats['partially_delivered']); ?></h3>
                            </div>
                            <div class="icon-box bg-warning-light">
                                <i class="bi bi-hourglass-split text-warning fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Fully Delivered</p>
                                <h3 class="mb-0 text-success"><?php echo e($stats['fully_delivered']); ?></h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Total Active</p>
                                <h3 class="mb-0 text-info"><?php echo e($stats['total_requests']); ?></h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="bi bi-box-seam text-info fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Approved Requests Pending Delivery</h4>
                    </div>
                    <div class="card-body">
                        <?php if($requests->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Project</th>
                                        <th>Requested By</th>
                                        <th>Items</th>
                                        <th>Delivery Progress</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $totalItems = $request->items->count();
                                    $itemsWithDeliveries = $request->items->filter(function($item) {
                                    return $item->deliveries->whereIn('verification_status', ['accepted',
                                    'partial'])->count() > 0;
                                    })->count();

                                    $totalQuantity = $request->items->sum('quantity');
                                    $deliveredQuantity = $request->items->sum(function($item) {
                                    return $item->deliveries->whereIn('verification_status', ['accepted',
                                    'partial'])->sum('quantity_delivered');
                                    });
                                    $progress = $totalQuantity > 0 ? round(($deliveredQuantity / $totalQuantity) * 100)
                                    : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('requests.show', $request)); ?>"
                                                class="fw-bold text-primary">
                                                <?php echo e($request->request_number); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <div><?php echo e($request->project->name); ?></div>
                                            <small class="text-muted"><?php echo e($request->project->code); ?></small>
                                        </td>
                                        <td>
                                            <?php if($request->requestedBy): ?>
                                            <?php echo e($request->requestedBy->firstname); ?> <?php echo e($request->requestedBy->lastname); ?>

                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary"><?php echo e($totalItems); ?> items</span>
                                        </td>
                                        <td style="min-width: 200px;">
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar <?php echo e($progress >= 100 ? 'bg-success' : ($progress > 0 ? 'bg-warning' : 'bg-secondary')); ?>"
                                                        role="progressbar" style="width: <?php echo e($progress); ?>%"
                                                        aria-valuenow="<?php echo e($progress); ?>" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <span
                                                    class="small fw-bold <?php echo e($progress >= 100 ? 'text-success' : 'text-muted'); ?>"><?php echo e($progress); ?>%</span>
                                            </div>
                                            <small class="text-muted"><?php echo e($itemsWithDeliveries); ?>/<?php echo e($totalItems); ?> items
                                                started</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo e($request->status->badgeClass()); ?>">
                                                <?php echo e($request->status->label()); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('deliveries.index', $request)); ?>"
                                                class="btn btn-sm btn-info me-1" title="View Deliveries">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if(auth()->user()->hasPermission('record-deliveries')): ?>
                                            <a href="<?php echo e(route('deliveries.create', $request)); ?>"
                                                class="btn btn-sm btn-primary" title="Record Delivery">
                                                <i class="bi bi-plus-circle"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo e($requests->links('pagination::bootstrap-4')); ?>

                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="mt-3">No Approved Requests</h5>
                            <p class="text-muted">There are no approved requests pending delivery at this time.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/deliveries/all.blade.php ENDPATH**/ ?>