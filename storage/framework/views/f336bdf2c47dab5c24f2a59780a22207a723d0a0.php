<?php $__env->startSection('title', $request->request_number . ' | PPMS'); ?>
<?php
$RequestStatus = App\Enums\RequestStatus::class;
?>
<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><?php echo e($request->request_number); ?></h4>
                    <p class="mb-0">Procurement Request Details</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('requests.index')); ?>">Requests</a></li>
                    <li class="breadcrumb-item active"><?php echo e($request->request_number); ?></li>
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
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Request Details -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Request Information</h4>
                        <?php if($request->isEditable()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $request)): ?>
                        <a href="<?php echo e(route('requests.edit', $request)); ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                <span class="badge badge-<?php echo e($request->status->badgeClass()); ?> fs-6">
                                    <?php echo e($request->status->label()); ?>

                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Project</label>
                            <div>
                                <a href="<?php echo e(route('projects.show', $request->project)); ?>" class="fw-bold text-primary">
                                    <?php echo e($request->project->name); ?>

                                </a>
                                <p class="mb-0 small text-muted"><?php echo e($request->project->code); ?></p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Requested By</label>
                            <p class="mb-0">
                                <?php if($request->requestedBy): ?>
                                <?php echo e($request->requestedBy->firstname); ?> <?php echo e($request->requestedBy->lastname); ?><br>
                                <small class="text-muted"><?php echo e($request->request_date->format('M d, Y')); ?></small>
                                <?php else: ?>
                                <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <?php if($request->required_by_date): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Required By</label>
                            <p class="mb-0"><?php echo e($request->required_by_date->format('M d, Y')); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="small text-muted">Justification</label>
                            <p class="mb-0"><?php echo e($request->justification); ?></p>
                        </div>

                        <?php if($canViewPricing): ?>
                        <hr>

                        <?php if($request->total_quoted_amount > 0): ?>
                        <div class="mb-0">
                            <label class="small text-muted">Quoted Total</label>
                            <p class="mb-0 fw-bold text-primary fs-5">
                                ₦<?php echo e(number_format($request->total_quoted_amount, 2)); ?>

                            </p>
                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>Pricing information is not visible for your role.</small>
                        </div>
                        <?php endif; ?>

                        <?php if($request->isEditable()): ?>
                        <hr>
                        <form action="<?php echo e(route('requests.submit', $request)); ?>" method="POST"
                            onsubmit="return confirm('Submit this request for processing?');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send me-1"></i> Submit Request
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Status Timeline</h4>
                    </div>
                    <div class="card-body">
                        <?php if($request->statusHistory->count() > 0): ?>
                        <div class="timeline">
                            <?php $__currentLoopData = $request->statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="timeline-item">
                                <div
                                    class="timeline-marker bg-<?php echo e($history->to_status === 'approved' ? 'success' : ($history->to_status === 'rejected' ? 'danger' : 'primary')); ?>">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1"><?php echo e(ucfirst(str_replace('_', ' ', $history->to_status))); ?></h6>
                                    <p class="mb-1 small">
                                        By <?php echo e($history->changedBy->firstname); ?> <?php echo e($history->changedBy->lastname); ?>

                                    </p>
                                    <?php if($history->comments): ?>
                                    <p class="mb-1 small text-muted"><?php echo e($history->comments); ?></p>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <?php echo e($history->created_at?->format('M d, Y g:i A') ?? '—'); ?>

                                    </small>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">No status changes yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Request Items -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Requested Items (<?php echo e($request->items->count()); ?>)</h4>
                        <?php if(
                        auth()->user()->hasPermission('process-purchase-request') &&
                        $request->status === $RequestStatus::PENDING_DIRECTOR
                        ): ?>
                        <a href="<?php echo e(route('approvals.edit-assignment', $request->id)); ?>" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i> Edit Assignment
                        </a>
                        <?php endif; ?>
                        <?php if($request->items): ?>
                        <a href="<?php echo e(route('reports.request-detail', ['request' => $request, 'download' => '1'])); ?>"
                            class="btn btn-sm btn-info me-2" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> Download PDF
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Quantity</th>
                                        <?php if($canViewPricing): ?>
                                        <th>Vendor</th>
                                        <th>Quoted Price</th>
                                        <th>Quoted Total</th>
                                        <?php endif; ?>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <?php if(request()->status === $RequestStatus::APPROVED &&
                                        auth()->user()->hasPermission('record-deliveries')): ?>
                                        <th>Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo e($item->material->name); ?></div>
                                            <small class="text-muted"><?php echo e($item->material->code); ?></small>
                                        </td>
                                        <td><?php echo e(number_format($item->quantity, 2)); ?>

                                            <?php echo e($item->material->unit_of_measurement); ?>

                                        </td>
                                        <?php if($canViewPricing): ?>
                                        <!-- <td>₦<?php echo e(number_format($item->estimated_unit_price, 2)); ?></td>
                                        <td class="fw-bold">₦<?php echo e(number_format($item->estimated_total, 2)); ?></td> -->
                                        <td>
                                            <?php if($item->vendor): ?>
                                            <?php echo e($item->vendor->name); ?>

                                            <?php else: ?>
                                            <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($item->quoted_unit_price): ?>
                                            ₦<?php echo e(number_format($item->quoted_unit_price, 2)); ?>

                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold text-primary">
                                            <?php if($item->quoted_total): ?>
                                            ₦<?php echo e(number_format($item->quoted_total, 2)); ?>

                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if($item->remarks): ?>
                                            <small><?php echo e($item->remarks); ?></small>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($item->delivery_status === 'complete'): ?>
                                            <span class="badge bg-success">Complete</span>
                                            <?php elseif($item->delivery_status === 'partial'): ?>
                                            <span class="badge bg-warning">Partial</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php if(request()->status === $RequestStatus::APPROVED &&
                                        auth()->user()->hasPermission('record-deliveries')): ?>
                                        <td>
                                            <?php if(
                                            auth()->user()->hasPermission('record-deliveries') &&
                                            $item->delivery_status !== 'complete'
                                            ): ?>
                                            <a href="<?php echo e(route('deliveries.create', $request)); ?>"
                                                class="btn btn-sm btn-primary" title="Record Delivery">
                                                <i class="bi bi-plus-circle"></i> Record Delivery
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Receipts -->
                <?php if($canViewPricing): ?>
                <div class="card mt-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Payment Receipts</h4>
                        <?php if(auth()->user()->can('update', $request) ||
                        auth()->user()->hasPermission('process-purchase-request')): ?>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#uploadReceiptModal">
                            <i class="bi bi-upload me-1"></i> Upload Receipt
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if($request->paymentReceipts && $request->paymentReceipts->count() > 0): ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $request->paymentReceipts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receipt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 mb-3">
                                <div class="card border h-100">
                                    <?php if(preg_match('/\.(jpg|jpeg|png)$/i', $receipt->file_path)): ?>
                                    <div style="height: 120px; overflow: hidden; background-color: #f8f9fa;"
                                        class="d-flex align-items-center justify-content-center">
                                        <a href="<?php echo e(route('receipts.download', $receipt)); ?>" target="_blank">
                                            <i class="bi bi-image text-primary" style="font-size: 3rem;"></i>
                                        </a>
                                    </div>
                                    <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                        style="height: 120px;">
                                        <a href="<?php echo e(route('receipts.download', $receipt)); ?>" target="_blank">
                                            <i class="bi bi-file-pdf text-danger" style="font-size: 3rem;"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    <div class="card-body p-2">
                                        <p class="mb-1 small text-truncate fw-bold"
                                            title="<?php echo e($receipt->original_filename); ?>">
                                            <?php echo e($receipt->original_filename); ?>

                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted d-block text-truncate" style="max-width: 120px;"
                                                title="<?php echo e($receipt->vendor ? $receipt->vendor->name : 'No vendor'); ?>">
                                                <i
                                                    class="bi bi-shop me-1"></i><?php echo e($receipt->vendor ? $receipt->vendor->name : 'No vendor'); ?>

                                            </small>
                                            <a href="<?php echo e(route('receipts.download', $receipt)); ?>" target="_blank"
                                                class="btn btn-xs btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center text-muted small py-3">No receipts uploaded yet</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upload Receipt Modal -->
<?php if(auth()->user()->can('update', $request) || auth()->user()->hasPermission('process-purchase-request')): ?>
<div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('requests.receipts.store', $request)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Upload Payment Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">Select Vendor...</option>
                            <?php
                            $requestVendors = collect();
                            if($request->items) {
                            $requestVendors = $request->items->pluck('vendor')->filter()->unique('id');
                            }
                            ?>
                            <?php $__currentLoopData = $requestVendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($vendor->id); ?>"><?php echo e($vendor->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="form-text">Select the vendor this payment was made to.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receipt Files <span class="text-danger">*</span></label>
                        <input type="file" name="receipts[]" class="form-control" multiple accept=".pdf,.jpeg,.png,.jpg"
                            required>
                        <div class="form-text">Max size 5MB per file. You can select multiple files.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Receipts</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border-color);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -26px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid var(--bg-card);
    }

    .timeline-content {
        padding-left: 10px;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/requests/show.blade.php ENDPATH**/ ?>