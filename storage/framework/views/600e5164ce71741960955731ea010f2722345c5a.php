<?php $__env->startSection('title', $vendor->name . ' | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><?php echo e($vendor->name); ?></h4>
                    <p class="mb-0">Vendor Details</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('vendors.index')); ?>">Vendors</a></li>
                    <li class="breadcrumb-item active"><?php echo e($vendor->name); ?></li>
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

        <div class="row">
            <!-- Vendor Details -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="card-title mb-0">Vendor Information</h4>
                            </div>
                        </div>
                        <?php if(auth()->user()->can('manage-vendors')): ?>
                        <a href="<?php echo e(route('vendors.edit', $vendor)); ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                <?php if($vendor->status === 'active'): ?>
                                <span class="badge badge-success fs-6">Active</span>
                                <?php elseif($vendor->status === 'suspended'): ?>
                                <span class="badge badge-warning fs-6">Suspended</span>
                                <?php else: ?>
                                <span class="badge badge-secondary fs-6">Inactive</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($vendor->rating): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Rating</label>
                            <div class="d-flex align-items-center">
                                <span class="me-2 fw-bold fs-5"><?php echo e(number_format($vendor->rating, 1)); ?></span>
                                <?php for($i = 1; $i <= 5; $i++): ?> <i
                                    class="bi bi-star<?php echo e($i <= $vendor->rating ? '-fill text-warning' : ''); ?> me-1"></i>
                                    <?php endfor; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($vendor->business_registration): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Registration Number</label>
                            <p class="mb-0"><?php echo e($vendor->business_registration); ?></p>
                        </div>
                        <?php endif; ?>

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Contact Person</label>
                            <p class="mb-0 fw-bold"><?php echo e($vendor->contact_person); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Email</label>
                            <p class="mb-0">
                                <a href="mailto:<?php echo e($vendor->email); ?>" class="text-primary">
                                    <i class="bi bi-envelope me-1"></i><?php echo e($vendor->email); ?>

                                </a>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Phone</label>
                            <p class="mb-0">
                                <a href="tel:<?php echo e($vendor->phone); ?>" class="text-primary">
                                    <i class="bi bi-telephone me-1"></i><?php echo e($vendor->phone); ?>

                                </a>
                            </p>
                        </div>

                        <?php if($vendor->alt_phone): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Alternative Phone</label>
                            <p class="mb-0">
                                <a href="tel:<?php echo e($vendor->alt_phone); ?>" class="text-primary">
                                    <i class="bi bi-telephone me-1"></i><?php echo e($vendor->alt_phone); ?>

                                </a>
                            </p>
                        </div>
                        <?php endif; ?>

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Address</label>
                            <p class="mb-0"><?php echo e($vendor->address); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Location</label>
                            <p class="mb-0">
                                <?php if($vendor->state): ?>
                                <?php echo e($vendor->state->state); ?>

                                <?php if($vendor->lga): ?>, <?php echo e($vendor->lga->lga); ?><?php endif; ?>
                                <?php else: ?>
                                <span class="text-muted">Not specified</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <?php if($vendor->bank_name || $vendor->bank_account): ?>
                        <hr>
                        <p class="mb-3 text-muted fs-14">Bank Details</p>

                        <?php if($vendor->bank_name): ?>
                        <div class="mb-2">
                            <label class="small text-muted">Bank Name</label>
                            <p class="mb-0"><?php echo e($vendor->bank_name); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($vendor->bank_account): ?>
                        <div class="mb-0">
                            <label class="small text-muted">Account Number</label>
                            <p class="mb-0"><?php echo e($vendor->bank_account); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($vendor->bank_account_name): ?>
                        <div class="mb-0">
                            <label class="small text-muted">Account Name</label>
                            <p class="mb-0"><?php echo e($vendor->bank_account_name); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($vendor->tax_id): ?>
                        <div class="mb-0">
                            <label class="small text-muted">Tax ID</label>
                            <p class="mb-0"><?php echo e($vendor->tax_id); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php if($vendor->notes): ?>
                        <hr>
                        <div class="mb-0">
                            <label class="small text-muted">Notes</label>
                            <p class="mb-0"><?php echo e($vendor->notes); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Materials & Transactions -->
            <div class="col-lg-8">
                <!-- Assigned Materials -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Assigned Materials</h4>
                        <?php if(auth()->user()->can('manage-vendors')): ?>
                        <a href="<?php echo e(route('vendors.materials.assign', $vendor)); ?>" class="btn btn-sm btn-success me-1">
                            <i class="bi bi-box-seam"></i> Assign Materials
                        </a>
                        <?php endif; ?>
                        <?php if(auth()->user()->hasAnyRole(['admin', 'super_admin', 'director', 'procurement_officer'])): ?>
                        <a href="<?php echo e(route('reports.vendor-transactions', ['vendor' => $vendor, 'download' => '1'])); ?>"
                            class="btn btn-sm btn-info me-2" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> Download Report
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($vendor->materials->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Unit Price</th>
                                    <th>Min. Order Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $vendor->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($material->name); ?></div>
                                        <small class="text-muted"><?php echo e($material->code); ?></small>
                                    </td>
                                    <td class="fw-bold text-success">
                                        ₦<?php echo e(number_format($material->pivot->price, 2)); ?>

                                        <small class="text-muted">/ <?php echo e($material->unit_of_measurement); ?></small>
                                    </td>
                                    <td>
                                        <?php if($material->pivot->minimum_order_quantity): ?>
                                        <?php echo e(number_format($material->pivot->minimum_order_quantity, 2)); ?>

                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted"></i>
                        <h5 class="mt-3">No Materials Assigned</h5>
                        <p class="text-muted">This vendor hasn't been assigned to any materials yet.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Transactions</h4>
                </div>
                <div class="card-body">
                    <?php
                    $recentItems = \App\Models\RequestItem::with(['procurementRequest', 'material'])
                    ->where('vendor_id', $vendor->id)
                    ->whereHas('procurementRequest', function($q) {
                    $q->whereNotIn('status', ['draft', 'rejected']);
                    })
                    ->latest()
                    ->limit(10)
                    ->get();
                    ?>

                    <?php if($recentItems->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Material</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('requests.show', $item->procurementRequest)); ?>"
                                            class="fw-bold text-primary">
                                            <?php echo e($item->procurementRequest->request_number); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <div><?php echo e($item->material->name); ?></div>
                                        <small class="text-muted"><?php echo e($item->material->code); ?></small>
                                    </td>
                                    <td><?php echo e(number_format($item->quantity, 2)); ?>

                                        <?php echo e($item->material->unit_of_measurement); ?>

                                    </td>
                                    <td>₦<?php echo e(number_format($item->quoted_unit_price, 2)); ?></td>
                                    <td class="fw-bold text-success">₦<?php echo e(number_format($item->quoted_total, 2)); ?>

                                    </td>
                                    <td><?php echo e($item->procurementRequest->request_date->format('M d, Y')); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo e($item->procurementRequest->status->badgeClass()); ?>">
                                            <?php echo e($item->procurementRequest->status->label()); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Transaction Summary -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <label class="small text-muted d-block">Total Transactions</label>
                                <span class="fs-4 fw-bold"><?php echo e($recentItems->count()); ?></span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">Total Value</label>
                                <span
                                    class="fs-4 fw-bold text-success">₦<?php echo e(number_format($recentItems->sum('quoted_total'), 2)); ?></span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">Approved Orders</label>
                                <span class="fs-4 fw-bold text-primary">
                                    <?php echo e($recentItems->filter(fn($i) => $i->procurementRequest->status->value === 'approved')->count()); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-receipt display-1 text-muted"></i>
                        <h5 class="mt-3">No Transactions Yet</h5>
                        <p class="text-muted">This vendor hasn't been used in any procurement requests.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/vendors/show.blade.php ENDPATH**/ ?>