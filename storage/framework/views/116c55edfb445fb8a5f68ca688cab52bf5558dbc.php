<?php $__env->startSection("title", "Audit Logs | PPMS"); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Audit Logs</h4>
                    <p class="mb-0">System activity and change history</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Audit Logs</li>
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

        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Filters Card -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel me-2"></i>Filters
                </h5>
                <button class="btn btn-sm btn-link" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('audits')); ?>">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Event Type</label>
                                <select name="event" class="form-select form-select-sm">
                                    <option value="">All Events</option>
                                    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($event); ?>" <?php echo e(request('event') == $event ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst($event)); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-select form-select-sm">
                                    <option value="">All Users</option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($u->id); ?>" <?php echo e(request('user_id') == $u->id ? 'selected' : ''); ?>>
                                        <?php echo e($u->firstname); ?> <?php echo e($u->lastname); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Model</label>
                                <select name="model_type" class="form-select form-select-sm">
                                    <option value="">All Models</option>
                                    <?php $__currentLoopData = $modelTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type['value']); ?>"
                                        <?php echo e(request('model_type') == $type['value'] ? 'selected' : ''); ?>>
                                        <?php echo e($type['label']); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="<?php echo e(request('date_from')); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="<?php echo e(request('date_to')); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="<?php echo e(route('audits')); ?>" class="btn btn-outline-secondary btn-md">
                                    Reset
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" name="q" value="<?php echo e(request('q')); ?>"
                                        placeholder="Search by user, old/new values...">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Audit Records
                    <span class="badge badge-info ms-2"><?php echo e($audits->total()); ?> total</span>
                </h4>
            </div>
            <div class="card-body">
                <?php if($audits->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Model</th>
                                <th>Timestamp</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $audits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration + ($audits->currentPage() - 1) * $audits->perPage()); ?></td>
                                <td>
                                    <?php if($audit->email): ?>
                                    <div><?php echo e($audit->firstname); ?> <?php echo e($audit->lastname); ?></div>
                                    <small class="text-muted"><?php echo e($audit->email); ?></small>
                                    <?php else: ?>
                                    <span class="text-muted">System</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($audit->event === 'created'): ?>
                                    <span class="badge badge-success">Created</span>
                                    <?php elseif($audit->event === 'updated'): ?>
                                    <span class="badge badge-warning">Updated</span>
                                    <?php elseif($audit->event === 'deleted'): ?>
                                    <span class="badge badge-danger">Deleted</span>
                                    <?php else: ?>
                                    <span class="badge badge-secondary"><?php echo e(ucfirst($audit->event)); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?php echo e(class_basename($audit->auditable_type)); ?></span>
                                    <small class="text-muted d-block">ID: <?php echo e($audit->auditable_id); ?></small>
                                </td>
                                <td>
                                    <div><?php echo e($audit->created_at->format('M d, Y')); ?></div>
                                    <small class="text-muted"><?php echo e($audit->created_at->format('g:i A')); ?></small>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('view-audit', $audit->id)); ?>" class="btn btn-sm btn-info"
                                        title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    <?php echo e($audits->links('pagination::bootstrap-4')); ?>

                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="mt-3">No Audit Records Found</h5>
                    <p class="text-muted">Try adjusting your filters or search criteria.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/audits/index.blade.php ENDPATH**/ ?>