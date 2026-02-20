<?php $__env->startSection('title', $project->name . ' | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><?php echo e($project->name); ?></h4>
                    <p class="mb-0"><?php echo e($project->code); ?></p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('projects.index')); ?>">Projects</a></li>
                    <li class="breadcrumb-item active"><?php echo e($project->code); ?></li>
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

        <!-- Analytics Cards -->
        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div
                                class="avatar-sm bg-primary-light rounded me-3 d-flex align-items-center justify-content-center text-primary">
                                <i class="bi bi-wallet2 fs-5"></i>
                            </div>
                            <span class="fs-14 d-block">Total Budget</span>
                        </div>
                        <h4 class="mb-0">₦<?php echo e(number_format($project->budget, 2)); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div
                                class="avatar-sm bg-success-light rounded me-3 d-flex align-items-center justify-content-center text-success">
                                <i class="bi bi-cash-stack fs-5"></i>
                            </div>
                            <span class="fs-14 d-block">Total Spent</span>
                        </div>
                        <h4 class="mb-0">₦<?php echo e(number_format($stats['spent_amount'], 2)); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div
                                class="avatar-sm bg-warning-light rounded me-3 d-flex align-items-center justify-content-center text-warning">
                                <i class="bi bi-pie-chart fs-5"></i>
                            </div>
                            <span class="fs-14 d-block">Budget Usage</span>
                        </div>
                        <h4 class="mb-0"><?php echo e($stats['budget_percentage']); ?>%</h4>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div
                                class="avatar-sm bg-info-light rounded me-3 d-flex align-items-center justify-content-center text-info">
                                <i class="bi bi-file-text fs-5"></i>
                            </div>
                            <span class="fs-14 d-block">Requests</span>
                        </div>
                        <h4 class="mb-0"><?php echo e($stats['total_requests']); ?> <small
                                class="text-muted fs-6 fw-normal">(<?php echo e($stats['pending_requests']); ?> pending)</small>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column: Budget & Requests -->
            <div class="col-lg-8">
                <!-- Budget Progress -->
                <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title">Budget Utilization</h4>
                    </div>
                    <div class="card-body pt-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Progress</span>
                            <span class="fw-bold"><?php echo e($stats['budget_percentage']); ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-<?php echo e($stats['budget_percentage'] > 90 ? 'danger' : ($stats['budget_percentage'] > 75 ? 'warning' : 'success')); ?>"
                                role="progressbar" style="width: <?php echo e($stats['budget_percentage']); ?>%"
                                aria-valuenow="<?php echo e($stats['budget_percentage']); ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-4">
                                <small class="text-muted d-block">Available Funds</small>
                                <h5 class="text-secondary fw-bold">
                                    ₦<?php echo e(number_format($project->budget - $stats['spent_amount'], 2)); ?></h5>
                            </div>
                            <div class="col-sm-4 border-start border-end text-center">
                                <small class="text-muted d-block">Approved Requests</small>
                                <h5 class="text-primary fw-bold"><?php echo e($stats['approved_requests']); ?></h5>
                            </div>
                            <div class="col-sm-4 text-end">
                                <small class="text-muted d-block">Pending Requests</small>
                                <h5 class="text-warning fw-bold"><?php echo e($stats['pending_requests']); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Procurement Requests Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h4 class="card-title mb-0">Procurement Activity</h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\ProcurementRequest::class)): ?>
                        <?php if($project->isAssignedTo(auth()->user(), 'site_manager')): ?>
                        <a href="<?php echo e(route('requests.create', ['project_id' => $project->id])); ?>"
                            class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Request
                        </a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <?php if($project->procurementRequests->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-top-0">Request #</th>
                                        <th class="border-top-0">Requested By</th>
                                        <th class="border-top-0">Date</th>
                                        <th class="border-top-0">Status</th>
                                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                        <th class="border-top-0 text-end">Amount</th>
                                        <?php endif; ?>
                                        <th class="border-top-0 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $project->procurementRequests->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('requests.show', $req)); ?>" class="fw-bold text-primary">
                                                <?php echo e($req->request_number); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span
                                                        class="avatar-title rounded-circle bg-light text-secondary small">
                                                        <?php echo e(strtoupper(substr($req->requestedBy->firstname ?? 'U', 0, 1))); ?>

                                                    </span>
                                                </div>
                                                <?php echo e(optional($req->requestedBy)->firstname ?? 'N/A'); ?>

                                            </div>
                                        </td>
                                        <td><?php echo e($req->request_date->format('M d')); ?></td>
                                        <td>
                                            <span
                                                class="badge badge-subtle-<?php echo e($req->status->badgeClass()); ?> text-<?php echo e($req->status->badgeClass()); ?>">
                                                <?php echo e($req->status->label()); ?>

                                            </span>
                                        </td>
                                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                        <td class="text-end fw-bold">
                                            <?php if($req->total_quoted_amount): ?>
                                            ₦<?php echo e(number_format($req->total_quoted_amount, 2)); ?>

                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                        <td class="text-end">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $req)): ?>
                                            <a href="<?php echo e(route('requests.show', $req)); ?>"
                                                class="btn btn-xs btn-outline-info">
                                                View
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <?php if($project->procurementRequests->count() > 5): ?>
                            <div class="text-center p-3 border-top">
                                <a href="<?php echo e(route('projects.requests', $project)); ?>"
                                    class="text-primary text-decoration-none">View All Requests</a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="mt-2 text-muted">No procurement activity yet</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Team -->
            <div class="col-lg-4">
                <!-- Project Info -->
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Project Info&nbsp;</h4>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $project)): ?>
                            <a title="Edit" href="<?php echo e(route('projects.edit', $project)); ?>" class="text-muted"><i
                                    class="bi bi-pencil text-primary"></i></a>
                            <?php endif; ?>
                        </div>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $project)): ?>
                        <a title="Download Summary Report"
                            href="<?php echo e(route('reports.project-summary', ['project' => $project, 'download' => '1'])); ?>"
                            class="btn btn-sm btn-info me-2" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> Download PDF
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Status</label>
                            <div>
                                <span class="badge badge-<?php echo e($project->getStatusBadgeClass()); ?> px-3 py-2">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $project->status))); ?>

                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Duration</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-range me-2 text-primary"></i>
                                <?php if($project->start_date && $project->end_date): ?>
                                <?php echo e($project->start_date->format('M d, Y')); ?> -
                                <?php echo e($project->end_date->format('M d, Y')); ?>

                                <?php else: ?>
                                <span class="text-muted">Not scheduled</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Location</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2 text-danger"></i>
                                <?php echo e($project->location ?? 'Not specified'); ?>

                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small">Description</label>
                            <p class="small mb-0"><?php echo e(Str::limit($project->description ?? 'No description.', 150)); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title mb-0">Project Team</h4>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-3">Site Managers</h6>
                        <?php $__empty_1 = true; $__currentLoopData = $project->siteManagers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-2">
                                <div class="avatar-title bg-primary-light text-primary rounded-circle">
                                    <?php echo e(substr($sm->firstname, 0, 1)); ?><?php echo e(substr($sm->lastname, 0, 1)); ?>

                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0 small fw-bold"><?php echo e($sm->firstname); ?> <?php echo e($sm->lastname); ?></h6>
                                <small class="text-muted"><?php echo e($sm->email); ?></small>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <span class="text-muted small d-block mb-3">No active site managers</span>
                        <?php endif; ?>

                        <h6 class="text-muted small text-uppercase fw-bold mb-3 mt-4">Procurement Officers</h6>
                        <?php $__empty_1 = true; $__currentLoopData = $project->procurementOfficers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-2">
                                <div class="avatar-title bg-info-light text-info rounded-circle">
                                    <?php echo e(substr($po->firstname, 0, 1)); ?><?php echo e(substr($po->lastname, 0, 1)); ?>

                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0 small fw-bold"><?php echo e($po->firstname); ?> <?php echo e($po->lastname); ?></h6>
                                <small class="text-muted"><?php echo e($po->phone); ?></small>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <span class="text-muted small d-block mb-3">No active procurement officers</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="card">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Files</h4>
                        <span class="badge bg-secondary"><?php echo e(count($project->attachments ?? [])); ?></span>
                    </div>
                    <div class="card-body">
                        <?php if($project->attachments && count($project->attachments) > 0): ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $project->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border h-100">
                                    <?php if(str_starts_with($attachment['type'] ?? '', 'image/')): ?>
                                    <div style="height: 150px; overflow: hidden; background-color: #f8f9fa;"
                                        class="d-flex align-items-center justify-content-center">
                                        <img src="<?php echo e(Storage::url($attachment['path'])); ?>" class="card-img-top"
                                            alt="<?php echo e($attachment['original_name']); ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                        style="height: 150px;">
                                        <i class="bi bi-file-pdf text-danger" style="font-size: 3rem;"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="card-body p-2">
                                        <p class="mb-1 small text-truncate fw-bold"
                                            title="<?php echo e($attachment['original_name']); ?>">
                                            <?php echo e($attachment['original_name']); ?>

                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small
                                                class="text-muted"><?php echo e(number_format(($attachment['size'] ?? 0) / 1024, 0)); ?>

                                                KB</small>

                                            <div class="btn-group">
                                                <a href="<?php echo e(Storage::url($attachment['path'])); ?>" target="_blank"
                                                    class="btn btn-xs btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin'])): ?>
                                                <form action="<?php echo e(route('projects.delete-attachment', $project)); ?>"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Delete this attachment?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="index" value="<?php echo e($index); ?>">
                                                    <button type="submit" class="btn btn-xs btn-outline-danger"
                                                        title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center text-muted small py-3">No files uploaded</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .avatar-xs {
        width: 30px;
        height: 30px;
    }

    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .badge-subtle-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .badge-subtle-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .badge-subtle-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .badge-subtle-info {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }

    .extra-small {
        font-size: 0.75rem;
    }

    .fs-14 {
        font-size: 14px;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/projects/show.blade.php ENDPATH**/ ?>