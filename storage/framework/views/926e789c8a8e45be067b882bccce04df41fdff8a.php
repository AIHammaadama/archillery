<?php $__env->startSection('title', 'Procurement Queue | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Procurement Queue</h4>
                    <p class="mb-0">Requests awaiting vendor assignment</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Procurement Queue</li>
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

        <!-- Requests list -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pending Requests (<?php echo e($requests->total()); ?>)</h4>
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
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('requests.show', $req)); ?>" class="fw-bold text-primary">
                                                <?php echo e($req->request_number); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo e($req->project->name); ?></div>
                                            <small class="text-muted"><?php echo e($req->project->code); ?></small>
                                        </td>
                                        <td>
                                            <?php if($req->requestedBy): ?>
                                            <?php echo e($req->requestedBy->firstname); ?> <?php echo e($req->requestedBy->lastname); ?>

                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($req->request_date->format('M d, Y')); ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo e($req->items->count()); ?> items</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo e($req->status->badgeClass()); ?>">
                                                <?php echo e($req->status->label()); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('approvals.assign-vendors', $req)); ?>"
                                                class="btn btn-sm btn-primary" title="Assign Vendors">
                                                <i class="bi bi-person-plus"></i> Assign
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if($requests->hasPages()): ?>
                        <div class="mt-4">
                            <?php echo e($requests->links()); ?>

                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="mt-3">No Pending Requests</h5>
                            <p class="text-muted">All procurement requests have been processed.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/approvals/procurement-queue.blade.php ENDPATH**/ ?>