<?php $__env->startSection('title', $delivery->delivery_number . ' | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><?php echo e($delivery->delivery_number); ?></h4>
                    <p class="mb-0">Delivery Details</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?php echo e(route('requests.show', $delivery->request)); ?>"><?php echo e($delivery->request->request_number); ?></a>
                    </li>
                    <li class="breadcrumb-item"><a
                            href="<?php echo e(route('deliveries.index', $delivery->request)); ?>">Deliveries</a></li>
                    <li class="breadcrumb-item active"><?php echo e($delivery->delivery_number); ?></li>
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

        <div class="row">
            <!-- Delivery Details -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Delivery Information</h4>
                        <?php if($delivery->verification_status === 'pending' &&
                        auth()->user()->hasPermission('verify-deliveries')): ?>
                        <a href="<?php echo e(route('deliveries.verify', $delivery)); ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-check-circle"></i> Verify
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                <?php if($delivery->verification_status === 'accepted'): ?>
                                <span class="badge badge-success fs-6">Accepted</span>
                                <?php elseif($delivery->verification_status === 'rejected'): ?>
                                <span class="badge badge-danger fs-6">Rejected</span>
                                <?php elseif($delivery->verification_status === 'partial'): ?>
                                <span class="badge badge-warning fs-6">Partial</span>
                                <?php else: ?>
                                <span class="badge badge-secondary fs-6">Pending Verification</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Delivery Date</label>
                            <p class="mb-0"><?php echo e($delivery->delivery_date->format('M d, Y')); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Quantity Delivered</label>
                            <p class="mb-0 fw-bold fs-5">
                                <?php echo e(number_format($delivery->quantity_delivered, 2)); ?>

                                <?php echo e($delivery->requestItem->material->unit_of_measurement); ?>

                            </p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Material</label>
                            <div class="fw-bold"><?php echo e($delivery->requestItem->material->name); ?></div>
                            <small class="text-muted"><?php echo e($delivery->requestItem->material->code); ?></small>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Vendor</label>
                            <p class="mb-0"><?php echo e($delivery->vendor ? $delivery->vendor->name : 'N/A'); ?></p>
                        </div>

                        <?php if($delivery->waybill_number): ?>
                        <hr>
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
                        <?php endif; ?>

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Received By</label>
                            <p class="mb-0">
                                <?php if($delivery->receivedBy): ?>
                                <?php echo e($delivery->receivedBy->firstname); ?> <?php echo e($delivery->receivedBy->lastname); ?><br>
                                <small class="text-muted"><?php echo e($delivery->created_at->format('M d, Y g:i A')); ?></small>
                                <?php else: ?>
                                <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <?php if($delivery->verifiedBy): ?>
                        <div class="mb-0">
                            <label class="small text-muted">Verified By</label>
                            <p class="mb-0">
                                <?php echo e($delivery->verifiedBy->firstname); ?> <?php echo e($delivery->verifiedBy->lastname); ?><br>
                                <small class="text-muted"><?php echo e($delivery->updated_at->format('M d, Y g:i A')); ?></small>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quality Notes & Attachments -->
            <div class="col-lg-8">
                <!-- Site Manager Feedback -->
                <?php if($delivery->siteManagerCanUpdate()): ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Site Manager Feedback</h4>
                        <?php if(auth()->user()->hasRole('site_manager') &&
                        $delivery->request->project->site_manager_id === auth()->id()): ?>
                        <a href="<?php echo e(route('deliveries.update-status', $delivery)); ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i>
                            <?php echo e($delivery->siteManagerHasUpdated() ? 'Update' : 'Add'); ?> Feedback
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if($delivery->siteManagerHasUpdated()): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                <?php if($delivery->site_manager_status === 'received'): ?>
                                <span class="badge badge-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i> Received - Good Condition
                                </span>
                                <?php elseif($delivery->site_manager_status === 'issues_noted'): ?>
                                <span class="badge badge-warning fs-6">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Issues Noted
                                </span>
                                <?php elseif($delivery->site_manager_status === 'completed'): ?>
                                <span class="badge badge-primary fs-6">
                                    <i class="bi bi-check-circle-fill me-1"></i> Completed
                                </span>
                                <?php else: ?>
                                <span class="badge badge-secondary fs-6">Pending</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Comments</label>
                            <p class="mb-0"><?php echo e($delivery->site_manager_comments); ?></p>
                        </div>

                        <div class="mb-0">
                            <label class="small text-muted">Updated By</label>
                            <p class="mb-0">
                                <?php echo e($delivery->siteManagerUpdatedBy->firstname); ?>

                                <?php echo e($delivery->siteManagerUpdatedBy->lastname); ?><br>
                                <small
                                    class="text-muted"><?php echo e($delivery->site_manager_updated_at->format('M d, Y g:i A')); ?></small>
                            </p>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Site manager has not provided feedback on this delivery yet.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quality Notes -->
                <?php if($delivery->quality_notes): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Quality Notes</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?php echo e($delivery->quality_notes); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Attachments -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Attachments</h4>
                    </div>
                    <div class="card-body">
                        <?php if($delivery->attachments && count($delivery->attachments) > 0): ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $delivery->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4">
                                <div class="card border">
                                    <?php if(str_starts_with($attachment['type'], 'image/')): ?>
                                    <img src="<?php echo e(Storage::url($attachment['path'])); ?>" class="card-img-top"
                                        alt="<?php echo e($attachment['original_name']); ?>"
                                        style="max-height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                        style="height: 200px;">
                                        <i class="bi bi-file-pdf text-danger" style="font-size: 4rem;"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="card-body p-2">
                                        <p class="mb-1 small text-truncate" title="<?php echo e($attachment['original_name']); ?>">
                                            <?php echo e($attachment['original_name']); ?>

                                        </p>
                                        <small class="text-muted"><?php echo e(number_format($attachment['size'] / 1024, 2)); ?>

                                            KB</small>
                                        <div class="mt-2">
                                            <a href="<?php echo e(Storage::url($attachment['path'])); ?>" target="_blank"
                                                class="btn btn-sm btn-info w-100 mb-1">
                                                <i class="bi bi-eye me-1"></i> View
                                            </a>
                                            <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin'])): ?>
                                            <form action="<?php echo e(route('deliveries.delete-attachment', $delivery)); ?>"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this attachment?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <input type="hidden" name="index" value="<?php echo e($index); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark display-1 text-muted"></i>
                            <h5 class="mt-3">No Attachments</h5>
                            <p class="text-muted">No photos or documents were uploaded with this delivery.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Request Context -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Request Context</h4>
                        <a href="<?php echo e(route('reports.delivery-receipt', ['delivery' => $delivery, 'download' => '1'])); ?>"
                            class="btn btn-sm btn-info me-2" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> Download Receipt
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Request Number</label>
                                <div>
                                    <a href="<?php echo e(route('requests.show', $delivery->request)); ?>"
                                        class="fw-bold text-primary">
                                        <?php echo e($delivery->request->request_number); ?>

                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Project</label>
                                <div><?php echo e($delivery->request->project->name); ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Ordered Quantity</label>
                                <div><?php echo e(number_format($delivery->requestItem->quantity, 2)); ?>

                                    <?php echo e($delivery->requestItem->material->unit_of_measurement); ?>

                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Total Delivered (All Deliveries)</label>
                                <?php
                                $totalDelivered = $delivery->requestItem->deliveries()
                                ->whereIn('verification_status', ['accepted', 'partial'])
                                ->sum('quantity_delivered');
                                ?>
                                <div
                                    class="<?php echo e($totalDelivered >= $delivery->requestItem->quantity ? 'text-success fw-bold' : 'text-warning'); ?>">
                                    <?php echo e(number_format($totalDelivered, 2)); ?>

                                    <?php echo e($delivery->requestItem->material->unit_of_measurement); ?>

                                    <?php if($totalDelivered >= $delivery->requestItem->quantity): ?>
                                    <i class="bi bi-check-circle ms-1"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/deliveries/show.blade.php ENDPATH**/ ?>