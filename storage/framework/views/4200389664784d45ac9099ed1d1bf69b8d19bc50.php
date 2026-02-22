<?php $__env->startSection('title', 'Verify Delivery | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Verify Delivery</h4>
                    <p class="mb-0"><?php echo e($delivery->delivery_number); ?></p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?php echo e(route('requests.show', $delivery->request)); ?>"><?php echo e($delivery->request->request_number); ?></a>
                    </li>
                    <li class="breadcrumb-item"><a
                            href="<?php echo e(route('deliveries.show', $delivery)); ?>"><?php echo e($delivery->delivery_number); ?></a></li>
                    <li class="breadcrumb-item active">Verify</li>
                </ol>
            </div>
        </div>

        <!-- Alerts -->
        <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Delivery Details (Read-only) -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Delivery Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Material</label>
                            <div class="fw-bold"><?php echo e($delivery->requestItem->material->name); ?></div>
                            <small class="text-muted"><?php echo e($delivery->requestItem->material->code); ?></small>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Vendor</label>
                            <p class="mb-0"><?php echo e($delivery->vendor ? $delivery->vendor->name : 'N/A'); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Delivery Date</label>
                            <p class="mb-0"><?php echo e($delivery->delivery_date->format('M d, Y')); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Quantity Delivered</label>
                            <p class="mb-0 fw-bold text-primary fs-5">
                                <?php echo e(number_format($delivery->quantity_delivered, 2)); ?>

                                <?php echo e($delivery->requestItem->material->unit_of_measurement); ?>

                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Ordered Quantity</label>
                            <p class="mb-0"><?php echo e(number_format($delivery->requestItem->quantity, 2)); ?>

                                <?php echo e($delivery->requestItem->material->unit_of_measurement); ?></p>
                        </div>

                        <hr>

                        <?php if($delivery->waybill_number): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Waybill Number</label>
                            <p class="mb-0"><?php echo e($delivery->waybill_number); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($delivery->invoice_number): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Invoice Number</label>
                            <p class="mb-0"><?php echo e($delivery->invoice_number); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($delivery->invoice_amount): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Invoice Amount</label>
                            <p class="mb-0 fw-bold text-success">₦<?php echo e(number_format($delivery->invoice_amount, 2)); ?></p>
                        </div>
                        <hr>
                        <?php endif; ?>



                        <div class="mb-0">
                            <label class="small text-muted">Received By</label>
                            <p class="mb-0">
                                <?php if($delivery->receivedBy): ?>
                                <?php echo e($delivery->receivedBy->firstname); ?> <?php echo e($delivery->receivedBy->lastname); ?><br>
                                <small class="text-muted"><?php echo e($delivery->created_at->format('M d, Y g:i A')); ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Attachments -->
                <?php if($delivery->attachments && count($delivery->attachments) > 0): ?>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Attachments</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php $__currentLoopData = $delivery->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6">
                                <div class="card border">
                                    <?php if(str_starts_with($attachment['type'], 'image/')): ?>
                                    <a href="<?php echo e(Storage::url($attachment['path'])); ?>" target="_blank">
                                        <img src="<?php echo e(Storage::url($attachment['path'])); ?>" class="card-img-top"
                                            alt="<?php echo e($attachment['original_name']); ?>"
                                            style="max-height: 150px; object-fit: cover;">
                                    </a>
                                    <?php else: ?>
                                    <a href="<?php echo e(Storage::url($attachment['path'])); ?>" target="_blank"
                                        class="card-img-top bg-light d-flex align-items-center justify-content-center text-decoration-none"
                                        style="height: 150px;">
                                        <i class="bi bi-file-pdf text-danger" style="font-size: 3rem;"></i>
                                    </a>
                                    <?php endif; ?>
                                    <div class="card-body p-2">
                                        <p class="mb-0 small text-truncate" title="<?php echo e($attachment['original_name']); ?>">
                                            <?php echo e($attachment['original_name']); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Verification Form -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Verification Decision</h4>
                    </div>
                    <div class="card-body">
                        <?php if($delivery->quality_notes): ?>
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading">Quality Notes from Receiver:</h6>
                            <p class="mb-0"><?php echo e($delivery->quality_notes); ?></p>
                        </div>
                        <?php endif; ?>

                        <form action="<?php echo e(route('deliveries.process-verification', $delivery)); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div class="mb-4">
                                <label class="form-label">Verification Status <span class="text-danger">*</span></label>

                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="verification_status"
                                        id="status_accepted" value="accepted"
                                        <?php echo e(old('verification_status') === 'accepted' ? 'checked' : ''); ?> required>
                                    <label class="form-check-label w-100" for="status_accepted">
                                        <div class="fw-bold text-success">
                                            <i class="bi bi-check-circle me-1"></i> Accept
                                        </div>
                                        <small class="text-muted">The delivery is in good condition and meets quality
                                            standards.</small>
                                    </label>
                                </div>

                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="verification_status"
                                        id="status_partial" value="partial"
                                        <?php echo e(old('verification_status') === 'partial' ? 'checked' : ''); ?> required>
                                    <label class="form-check-label w-100" for="status_partial">
                                        <div class="fw-bold text-warning">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Partial Accept
                                        </div>
                                        <small class="text-muted">The delivery has minor issues but is acceptable with
                                            notes.</small>
                                    </label>
                                </div>

                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="verification_status"
                                        id="status_rejected" value="rejected"
                                        <?php echo e(old('verification_status') === 'rejected' ? 'checked' : ''); ?> required>
                                    <label class="form-check-label w-100" for="status_rejected">
                                        <div class="fw-bold text-danger">
                                            <i class="bi bi-x-circle me-1"></i> Reject
                                        </div>
                                        <small class="text-muted">The delivery does not meet quality standards and must
                                            be returned.</small>
                                    </label>
                                </div>

                                <?php $__errorArgs = ['verification_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Verification Notes</label>
                                <textarea name="quality_notes"
                                    class="form-control <?php $__errorArgs = ['quality_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="4"
                                    placeholder="Add notes about quality, condition, compliance with specifications, issues found, etc."><?php echo e(old('quality_notes')); ?></textarea>
                                <small class="text-muted">These notes will be visible to all authorized users.</small>
                                <?php $__errorArgs = ['quality_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Action buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo e(route('deliveries.show', $delivery)); ?>" class="btn btn-light">
                                    <i class="bi bi-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('Submit verification decision?')">
                                    <i class="bi bi-check-circle me-1"></i> Submit Verification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/deliveries/verify.blade.php ENDPATH**/ ?>