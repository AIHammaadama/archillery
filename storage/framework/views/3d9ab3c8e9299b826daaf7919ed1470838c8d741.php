<?php $__env->startSection('title', $material->name . ' | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><?php echo e($material->name); ?></h4>
                    <p class="mb-0"><?php echo e($material->code); ?></p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('materials.index')); ?>">Materials</a></li>
                    <li class="breadcrumb-item active"><?php echo e($material->code); ?></li>
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

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Material Details</h4>
                        <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin'])): ?>
                        <a href="<?php echo e(route('materials.edit', $material)); ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                <?php if($material->is_active): ?>
                                <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                <span class="badge badge-secondary">Inactive</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($material->category): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Category</label>
                            <div><span class="badge badge-primary"><?php echo e($material->category); ?></span></div>
                        </div>
                        <?php endif; ?>

                        <?php if($material->unit_of_measurement): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Unit of Measurement</label>
                            <p class="mb-0"><?php echo e(ucfirst($material->unit_of_measurement)); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($material->description): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Description</label>
                            <p class="mb-0"><?php echo e($material->description); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($material->specifications): ?>
                        <div class="mb-0">
                            <label class="small text-muted">Specifications</label>
                            <div class="mt-2">
                                <?php $__currentLoopData = $material->specifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted"><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?>:</span>
                                    <span class="fw-bold"><?php echo e($value); ?></span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Vendor Pricing</h4>
                    </div>
                    <div class="card-body">
                        <?php if(auth()->user()->can('manage-vendors')): ?>
                        <?php if($material->vendors->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Price (<?php echo e($material->vendors->first()->pivot->currency ?? 'NGN'); ?>)</th>
                                        <th>Min Order Qty</th>
                                        <th>Lead Time</th>
                                        <th>Valid Until</th>
                                        <th>Preferred</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $material->vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo e($vendor->name); ?></div>
                                            <small class="text-muted"><?php echo e($vendor->code); ?></small>
                                        </td>
                                        <td class="fw-bold text-success">
                                            ₦<?php echo e(number_format($vendor->pivot->price, 2)); ?>

                                        </td>
                                        <td><?php echo e($vendor->pivot->minimum_order_quantity); ?>

                                            <?php echo e($material->unit_of_measurement); ?></td>
                                        <td><?php echo e($vendor->pivot->lead_time_days); ?> days</td>
                                        <td>
                                            <?php if($vendor->pivot->valid_until): ?>
                                            <?php echo e(\Carbon\Carbon::parse($vendor->pivot->valid_until)->format('M d, Y')); ?>

                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($vendor->pivot->is_preferred): ?>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <?php else: ?>
                                            <i class="bi bi-star text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-shop display-1 text-muted"></i>
                            <h5 class="mt-3">No Vendor Pricing Available</h5>
                            <p class="text-muted">No vendors have been assigned pricing for this material yet.</p>
                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Vendor pricing information is not available for your role.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/materials/show.blade.php ENDPATH**/ ?>