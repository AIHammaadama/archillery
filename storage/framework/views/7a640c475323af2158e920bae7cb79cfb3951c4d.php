<?php $__env->startSection('title', 'Director Approval Queue | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Director Approval Queue</h4>
                    <p class="mb-0">Requests awaiting your approval</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Approval Queue</li>
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

        <!-- Requests list -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pending Approvals (<?php echo e($requests->total()); ?>)</h4>
                    </div>
                    <div class="card-body">
                        <?php if($requests->count() > 0): ?>
                        <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card mb-3 border">
                            <div class="card-header bg-light">
                                <div class="row align-items-center ">
                                    <div class="col-md-7">
                                        <h5 class="mb-1">
                                            <a href="<?php echo e(route('requests.show', $req)); ?>" class="text-primary">
                                                <?php echo e($req->request_number); ?>

                                            </a>
                                        </h5>
                                        <div class="text-muted">
                                            <i class="bi bi-building me-1"></i><?php echo e($req->project->name); ?>

                                            <span class="mx-2">|</span>
                                            <i
                                                class="bi bi-calendar me-1"></i><?php echo e($req->request_date->format('M d, Y')); ?>

                                        </div>
                                    </div>
                                    <div class="col-md-5 text-end">
                                        <div class="fw-bold text-success fs-5">
                                            ₦<?php echo e(number_format($req->total_quoted_amount, 2)); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="small text-muted">Requested By</label>
                                        <div>
                                            <?php if($req->requestedBy): ?>
                                            <?php echo e($req->requestedBy->firstname); ?> <?php echo e($req->requestedBy->lastname); ?>

                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted">Processed By</label>
                                        <div>
                                            <?php if($req->procurementOfficer): ?>
                                            <?php echo e($req->procurementOfficer->firstname); ?>

                                            <?php echo e($req->procurementOfficer->lastname); ?>

                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted">Items</label>
                                        <div><span class="badge badge-info"><?php echo e($req->items->count()); ?> items</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted">Required By</label>
                                        <div>
                                            <?php if($req->required_by_date): ?>
                                            <?php echo e($req->required_by_date->format('M d, Y')); ?>

                                            <?php else: ?>
                                            <span class="text-muted">Not specified</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="small text-muted">Justification</label>
                                    <p class="mb-0"><?php echo e($req->justification); ?></p>
                                </div>

                                <!-- Items summary -->
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Material</th>
                                                <th>Quantity</th>
                                                <th>Vendor</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $req->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <div><?php echo e($item->material->name); ?></div>
                                                    <small class="text-muted"><?php echo e($item->material->code); ?></small>
                                                </td>
                                                <td><?php echo e(number_format($item->quantity, 2)); ?>

                                                    <?php echo e($item->material->unit_of_measurement); ?>

                                                </td>
                                                <td><?php echo e($item->vendor ? $item->vendor->name : 'N/A'); ?></td>
                                                <td class="text-success">
                                                    ₦<?php echo e(number_format($item->quoted_unit_price, 2)); ?></td>
                                                <td class="fw-bold text-primary">
                                                    ₦<?php echo e(number_format($item->quoted_total, 2)); ?></td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Action buttons -->
                                <div class="d-flex flex-wrap gap-2 mt-3
            flex-column flex-sm-row align-items-stretch align-items-sm-center"
                                    x-data="{ showRejectModal: false, showSendBackModal: false, showApproveModal: false }">
                                    <button type="button" class="btn btn-success" @click="showApproveModal = true">
                                        <i class="bi bi-check-circle me-1"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger" @click="showRejectModal = true">
                                        <i class="bi bi-x-circle me-1"></i> Reject
                                    </button>
                                    <button type="button" class="btn btn-warning" @click="showSendBackModal = true">
                                        <i class="bi bi-arrow-return-left me-1"></i> Send Back
                                    </button>
                                    <a href="<?php echo e(route('approvals.edit-assignment', $req)); ?>" class="btn btn-primary">
                                        <i class="bi bi-pencil me-1"></i> Edit Assignment
                                    </a>
                                    <a href="<?php echo e(route('requests.show', $req)); ?>" class="btn btn-info ms-auto">
                                        <i class="bi bi-eye me-1"></i> View Details
                                    </a>

                                    <!-- Approve Modal -->
                                    <div x-show="showApproveModal" class="modal-backdrop" style="display: none;"
                                        x-transition>
                                        <div class="modal show d-block" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="<?php echo e(route('approvals.approve', $req)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Approve Request</h5>
                                                            <button type="button" class="btn-close"
                                                                @click="showApproveModal = false"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to approve this procurement
                                                                request?</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Comments (Optional)</label>
                                                                <textarea name="comments" class="form-control" rows="3"
                                                                    placeholder="Add any comments..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                @click="showApproveModal = false">Cancel</button>
                                                            <button type="submit" class="btn btn-success">Approve
                                                                Request</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div x-show="showRejectModal" class="modal-backdrop" style="display: none;"
                                        x-transition>
                                        <div class="modal show d-block" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="<?php echo e(route('approvals.reject', $req)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject Request</h5>
                                                            <button type="button" class="btn-close"
                                                                @click="showRejectModal = false"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-danger">Are you sure you want to reject this
                                                                request?</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Reason <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control" rows="3"
                                                                    required
                                                                    placeholder="Provide a reason for rejection..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                @click="showRejectModal = false">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject
                                                                Request</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Send Back Modal -->
                                    <div x-show="showSendBackModal" class="modal-backdrop" style="display: none;"
                                        x-transition>
                                        <div class="modal show d-block" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="<?php echo e(route('approvals.send-back', $req)); ?>"
                                                        method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Send Back for Revision</h5>
                                                            <button type="button" class="btn-close"
                                                                @click="showSendBackModal = false"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Send this request back to the procurement officer for
                                                                revision.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Reason <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control" rows="3"
                                                                    required
                                                                    placeholder="Explain what needs to be revised..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                @click="showSendBackModal = false">Cancel</button>
                                                            <button type="submit" class="btn btn-warning">Send
                                                                Back</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <!-- Pagination -->
                        <?php if($requests->hasPages()): ?>
                        <div class="mt-4">
                            <?php echo e($requests->links()); ?>

                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="mt-3">No Pending Approvals</h5>
                            <p class="text-muted">All requests have been reviewed.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
}

.modal.show {
    z-index: 1050;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/approvals/director-queue.blade.php ENDPATH**/ ?>