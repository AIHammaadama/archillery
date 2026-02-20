<?php $__env->startSection('title', 'Project Requests | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Project Requests</h4>
                    <p class="mb-0"><?php echo e($project->name); ?> (<?php echo e($project->code); ?>)</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('projects.index')); ?>">Projects</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('projects.show', $project)); ?>">Details</a></li>
                    <li class="breadcrumb-item active">Requests</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Procurement Requests</h4>

                        <div class="d-flex gap-2">
                            <!-- Filter Form -->
                            <form action="<?php echo e(route('projects.requests', $project)); ?>" method="GET" class="d-flex gap-2">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <?php $__currentLoopData = App\Enums\RequestStatus::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status->value); ?>"
                                        <?php echo e(request('status') == $status->value ? 'selected' : ''); ?>>
                                        <?php echo e($status->label()); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search..."
                                        value="<?php echo e(request('search')); ?>">
                                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                                </div>

                                <?php if(request('status') || request('search')): ?>
                                <a href="<?php echo e(route('projects.requests', $project)); ?>"
                                    class="btn btn-sm btn-outline-secondary" title="Clear Filters">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                                <?php endif; ?>
                            </form>

                            <?php if($project->isAssignedTo(auth()->user(), 'site_manager')): ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\ProcurementRequest::class)): ?>
                            <a href="<?php echo e(route('requests.create', ['project_id' => $project->id])); ?>"
                                class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> New Request
                            </a>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Date</th>
                                        <th>Requested By</th>
                                        <th>Description</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('requests.show', $request)); ?>"
                                                class="fw-bold text-primary">
                                                <?php echo e($request->request_number); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e($request->request_date->format('M d, Y')); ?></td>
                                        <td>
                                            <?php if($request->requestedBy): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span
                                                        class="avatar-title rounded-circle bg-light text-secondary small">
                                                        <?php echo e(strtoupper(substr($request->requestedBy->firstname, 0, 1))); ?>

                                                    </span>
                                                </div>
                                                <span><?php echo e($request->requestedBy->firstname); ?>

                                                    <?php echo e($request->requestedBy->lastname); ?></span>
                                            </div>
                                            <?php else: ?>
                                            <span class="text-muted">Unknown</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(Str::limit($request->description ?? 'No description', 50)); ?></td>
                                        <td>
                                            <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                            <span
                                                class="fw-bold">₦<?php echo e(number_format($request->total_estimated_amount, 2)); ?></span>
                                            <?php else: ?>
                                            <span class="text-muted fst-italic">Hidden</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-subtle-<?php echo e($request->status->badgeClass()); ?> text-<?php echo e($request->status->badgeClass()); ?>">
                                                <?php echo e($request->status->label()); ?>

                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="<?php echo e(route('requests.show', $request)); ?>"
                                                    class="btn btn-xs btn-primary" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                <p>No requests found for this project.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <?php echo e($requests->links('pagination::bootstrap-4')); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/projects/requests.blade.php ENDPATH**/ ?>