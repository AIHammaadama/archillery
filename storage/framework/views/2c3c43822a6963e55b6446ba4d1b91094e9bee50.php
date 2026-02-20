<?php $__env->startSection("title", "View Audit | PPMS"); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Audit Details</h4>
                    <p class="mb-0">View detailed audit information</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('audits')); ?>">Audits</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </div>
        </div>

        <div class="mb-3">
            <a href="<?php echo e(route('audits')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Audits
            </a>
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

        <div class="row">
            <!-- User & Event Info -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person me-2"></i>User Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Name</label>
                            <p class="mb-0 fw-bold"><?php echo e($audits[0]['name'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Email</label>
                            <p class="mb-0"><?php echo e($audits[0]['email'] ?? 'N/A'); ?></p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="small text-muted">Event Type</label>
                            <div>
                                <?php
                                    $event = $audits[0]['event'] ?? 'unknown';
                                ?>
                                <?php if($event === 'created'): ?>
                                <span class="badge badge-success fs-6">Created</span>
                                <?php elseif($event === 'updated'): ?>
                                <span class="badge badge-warning fs-6">Updated</span>
                                <?php elseif($event === 'deleted'): ?>
                                <span class="badge badge-danger fs-6">Deleted</span>
                                <?php else: ?>
                                <span class="badge badge-secondary fs-6"><?php echo e(ucfirst($event)); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Timestamp</label>
                            <p class="mb-0"><?php echo e($audits[0]['created_at'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-globe me-2"></i>Request Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">URL</label>
                            <p class="mb-0 text-break small"><?php echo e($audits[0]['url'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">IP Address</label>
                            <p class="mb-0"><code><?php echo e($audits[0]['ip_address'] ?? 'N/A'); ?></code></p>
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted">User Agent</label>
                            <p class="mb-0 small text-muted"><?php echo e($audits[0]['user_agent'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Changes -->
            <div class="col-lg-8">
                <div class="row">
                    <!-- Old Values -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="bi bi-dash-circle me-2"></i>Old Values
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if(empty($audits[0]['old_values'])): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-dash-circle display-4 text-muted"></i>
                                    <p class="text-muted mt-2">No previous values</p>
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <?php $__currentLoopData = $audits[0]['old_values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <th class="text-nowrap" style="width: 35%;"><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?></th>
                                                <td class="text-danger">
                                                    <?php if(is_array($value)): ?>
                                                        <pre class="mb-0 small"><?php echo e(json_encode($value, JSON_PRETTY_PRINT)); ?></pre>
                                                    <?php else: ?>
                                                        <?php echo e($value); ?>

                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- New Values -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="bi bi-plus-circle me-2"></i>New Values
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if(empty($audits[0]['new_values'])): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-dash-circle display-4 text-muted"></i>
                                    <p class="text-muted mt-2">No new values</p>
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <?php $__currentLoopData = $audits[0]['new_values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <th class="text-nowrap" style="width: 35%;"><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?></th>
                                                <td class="text-success">
                                                    <?php if(is_array($value)): ?>
                                                        <pre class="mb-0 small"><?php echo e(json_encode($value, JSON_PRETTY_PRINT)); ?></pre>
                                                    <?php else: ?>
                                                        <?php echo e($value); ?>

                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/audits/show.blade.php ENDPATH**/ ?>