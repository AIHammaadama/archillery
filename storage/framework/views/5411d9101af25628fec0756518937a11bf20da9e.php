<?php $__env->startSection('title', 'Assign Vendors | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Assign Vendors</h4>
                    <p class="mb-0"><?php echo e($request->request_number); ?></p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('approvals.procurement-queue')); ?>">Procurement
                            Queue</a></li>
                    <li class="breadcrumb-item active">Assign Vendors</li>
                </ol>
            </div>
        </div>

        <!-- Alerts -->
        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

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
            <!-- Request Info Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Request Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Project</label>
                            <div class="fw-bold"><?php echo e($request->project->name); ?></div>
                            <small class="text-muted"><?php echo e($request->project->code); ?></small>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Requested By</label>
                            <p class="mb-0">
                                <?php if($request->requestedBy): ?>
                                <?php echo e($request->requestedBy->firstname); ?> <?php echo e($request->requestedBy->lastname); ?><br>
                                <small class="text-muted"><?php echo e($request->request_date->format('M d, Y')); ?></small>
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

                        <hr>

                        <div class="mb-0">
                            <label class="small text-muted">Total Estimated Amount</label>
                            <p class="mb-0 fw-bold text-success fs-5">
                                ₦<?php echo e(number_format($request->total_estimated_amount, 2)); ?>

                            </p>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Instructions</h4>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 ps-3">
                            <li class="mb-2">Select a vendor for each item from the dropdown</li>
                            <li class="mb-2">Enter the quoted unit price from the vendor</li>
                            <li class="mb-2">Quoted total will be calculated automatically</li>
                            <li class="mb-0">Submit for director approval when complete</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Vendor Assignment Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Assign Vendors & Pricing</h4>
                    </div>
                    <div class="card-body" x-data="vendorAssignment()">
                        <form action="<?php echo e(route('approvals.save-assignments', $request)); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div x-data="vendorAssignment()" x-init="init()">
                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="30%">Material</th>
                                            <th width="10%">Quantity</th>
                                            <th width="20%">Vendor</th>
                                            <th width="20%">Quoted Price</th>
                                            <th width="20%">Quoted Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?php echo e($item->material->name); ?></div>
                                                <small class="text-muted"><?php echo e($item->material->code); ?></small>
                                                <input type="hidden" name="items[<?php echo e($index); ?>][item_id]"
                                                    value="<?php echo e($item->id); ?>">
                                            </td>

                                            <td>
                                                <?php echo e(number_format($item->quantity, 2)); ?><br>
                                                <small
                                                    class="text-muted"><?php echo e($item->material->unit_of_measurement); ?></small>
                                            </td>

                                            <td>
                                                <select name="items[<?php echo e($index); ?>][vendor_id]"
                                                    class="form-control form-select-sm"
                                                    x-on:change="fetchVendorPrice(<?php echo e($index); ?>, $event.target.value, <?php echo e($item->material_id); ?>)"
                                                    required>
                                                    <option value="">Select Vendor</option>
                                                    <?php
                                                        $recommendedIds = $item->material->vendors->pluck('id')->toArray();
                                                    ?>

                                                    <?php if(count($recommendedIds) > 0): ?>
                                                        <optgroup label="Recommended (Sell this material)">
                                                            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php if(in_array($vendor->id, $recommendedIds)): ?>
                                                                    <option value="<?php echo e($vendor->id); ?>"
                                                                        <?php echo e(old('items.'.$index.'.vendor_id', $item->vendor_id) == $vendor->id ? 'selected' : ''); ?>>
                                                                        <?php echo e($vendor->name); ?>

                                                                    </option>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </optgroup>
                                                        <optgroup label="Other Vendors">
                                                            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php if(!in_array($vendor->id, $recommendedIds)): ?>
                                                                    <option value="<?php echo e($vendor->id); ?>"
                                                                        <?php echo e(old('items.'.$index.'.vendor_id', $item->vendor_id) == $vendor->id ? 'selected' : ''); ?>>
                                                                        <?php echo e($vendor->name); ?>

                                                                    </option>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </optgroup>
                                                    <?php else: ?>
                                                        <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($vendor->id); ?>"
                                                                <?php echo e(old('items.'.$index.'.vendor_id', $item->vendor_id) == $vendor->id ? 'selected' : ''); ?>>
                                                                <?php echo e($vendor->name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endif; ?>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="number" name="items[<?php echo e($index); ?>][quoted_unit_price]"
                                                    class="form-control form-control-sm"
                                                    x-model.number="items[<?php echo e($index); ?>].quotedPrice" step="0.01" min="0"
                                                    required>
                                            </td>

                                            <td class="fw-bold text-primary">
                                                <span x-text="'₦' + (items[<?php echo e($index); ?>].quotedPrice * <?php echo e($item->quantity); ?>)
                .toLocaleString('en-NG', { minimumFractionDigits: 2 })">
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                                            <td class="fw-bold text-primary fs-5">
                                                <span
                                                    x-text="'₦' + grandTotal.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Action buttons -->
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?php echo e(route('approvals.procurement-queue')); ?>" class="btn btn-light">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Queue
                                </a>
                                <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('Submit vendor assignments for director approval?')">
                                    <i class="bi bi-send me-1"></i> Submit for Approval
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$vendorItems = $request->items
->values()
->map(function ($item, $index) {
return [
'quantity' => $item->quantity,
'quotedPrice' => old(
"items.$index.quoted_unit_price",
$item->quoted_unit_price ?? 0
),
];
});
?>
<script>
    function vendorAssignment() {
        return {
            items: <?php echo json_encode($vendorItems, 15, 512) ?>,
            grandTotal: 0,
            loading: {},

            init() {
                // Initial calculation
                this.calculateGrandTotal();

                // Recalculate whenever inputs change
                this.$el.addEventListener('input', () => {
                    this.calculateGrandTotal();
                });
            },

            async fetchVendorPrice(itemIndex, vendorId, materialId) {
                if (!vendorId || !materialId) {
                    return;
                }

                // Set loading state
                this.loading[itemIndex] = true;

                try {
                    const response = await fetch(`/api/vendors/${vendorId}/materials/${materialId}/price`);

                    if (!response.ok) {
                        throw new Error('Failed to fetch price');
                    }

                    const data = await response.json();

                    // Auto-fill the price if available
                    if (data.price && data.is_valid) {
                        this.items[itemIndex].quotedPrice = parseFloat(data.price);

                        // Update the input field value
                        const priceInput = this.$el.querySelector(
                            `input[name="items[${itemIndex}][quoted_unit_price]"]`);
                        if (priceInput) {
                            priceInput.value = data.price;
                        }

                        // Recalculate totals
                        this.calculateGrandTotal();

                        // Show success feedback
                        console.log(`Price auto-filled: ₦${parseFloat(data.price).toLocaleString('en-NG')}`);
                    } else {
                        // no valid price - reset to 0 (or null)
                        this.items[itemIndex].quotedPrice = 0;

                        const priceInput = this.$el.querySelector(
                            `input[name="items[${itemIndex}][quoted_unit_price]"]`
                        );
                        if (priceInput) priceInput.value = 0;

                        this.calculateGrandTotal();
                    }
                } catch (error) {
                    console.error('Error fetching vendor price:', error);
                } finally {
                    this.loading[itemIndex] = false;
                }
            },

            calculateGrandTotal() {
                this.grandTotal = this.items.reduce((sum, item) => {
                    const qty = parseFloat(item.quantity) || 0;
                    const price = parseFloat(item.quotedPrice) || 0;
                    return sum + (qty * price);
                }, 0);
            }
        };
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/approvals/assign-vendors.blade.php ENDPATH**/ ?>