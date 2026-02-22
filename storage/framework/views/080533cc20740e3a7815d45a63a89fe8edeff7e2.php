<?php $__env->startSection('title', 'Record Delivery | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Record Delivery</h4>
                    <p class="mb-0"><?php echo e($request->request_number); ?></p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?php echo e(route('requests.show', $request)); ?>"><?php echo e($request->request_number); ?></a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('deliveries.index', $request)); ?>">Deliveries</a></li>
                    <li class="breadcrumb-item active">Record</li>
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
            <!-- Request Info -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Request Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Project</label>
                            <div class="fw-bold"><?php echo e($request->project->name); ?></div>
                        </div>
                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                        <div class="mb-3">
                            <label class="small text-muted">Total Quoted Amount</label>
                            <p class="mb-0 fw-bold text-success fs-5">
                                ₦<?php echo e(number_format($request->total_quoted_amount, 2)); ?>

                            </p>
                        </div>
                        <?php endif; ?>

                        <hr>

                        <h6 class="mb-3">Items Overview</h6>
                        <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $totalDelivered = $item->deliveries()
                        ->whereIn('verification_status', ['accepted', 'partial'])
                        ->sum('quantity_delivered');
                        $remaining = $item->quantity - $totalDelivered;
                        ?>

                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fw-bold"><?php echo e($item->material->name); ?></div>
                            <small class="text-muted"><?php echo e($item->material->code); ?></small>
                            <div class="mt-2">
                                <small class="text-muted d-block">Ordered: <?php echo e(number_format($item->quantity, 2)); ?>

                                    <?php echo e($item->material->unit_of_measurement); ?></small>
                                <small class="text-muted d-block">Delivered: <?php echo e(number_format($totalDelivered, 2)); ?>

                                    <?php echo e($item->material->unit_of_measurement); ?></small>
                                <small class="d-block <?php echo e($remaining > 0 ? 'text-warning' : 'text-success'); ?>">
                                    Remaining: <?php echo e(number_format($remaining, 2)); ?>

                                    <?php echo e($item->material->unit_of_measurement); ?>

                                </small>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Delivery Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Delivery Details</h4>
                    </div>
                    <div class="card-body" x-data="deliveryForm()">
                        <form action="<?php echo e(route('deliveries.store', $request)); ?>" method="POST"
                            enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="row">
                                <!-- Item Selection -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Item <span class="text-danger">*</span></label>
                                    <select name="request_item_id"
                                        class="form-control <?php $__errorArgs = ['request_item_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        x-model="selectedItemId" @change="updateItemDetails()" required>
                                        <option value="">Select Item</option>
                                        <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $totalDelivered = $item->deliveries()
                                        ->whereIn('verification_status', ['accepted', 'partial'])
                                        ->sum('quantity_delivered');
                                        $remaining = $item->quantity - $totalDelivered;
                                        ?>
                                        <?php if($remaining > 0): ?>
                                        <option value="<?php echo e($item->id); ?>" data-quantity="<?php echo e($item->quantity); ?>"
                                            data-delivered="<?php echo e($totalDelivered); ?>" data-remaining="<?php echo e($remaining); ?>"
                                            data-unit="<?php echo e($item->material->unit_of_measurement); ?>"
                                            <?php echo e(old('request_item_id') == $item->id ? 'selected' : ''); ?>>
                                            <?php echo e($item->material->name); ?> (<?php echo e(number_format($remaining, 2)); ?>

                                            <?php echo e($item->material->unit_of_measurement); ?> remaining)
                                        </option>
                                        <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['request_item_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                    <div x-show="selectedItemId" class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted d-block">Ordered: <span x-text="itemQuantity"></span>
                                            <span x-text="itemUnit"></span></small>
                                        <small class="text-muted d-block">Delivered: <span
                                                x-text="itemDelivered"></span> <span x-text="itemUnit"></span></small>
                                        <small class="fw-bold text-warning d-block">Remaining: <span
                                                x-text="itemRemaining"></span> <span x-text="itemUnit"></span></small>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Delivery Date <span class="text-danger">*</span></label>
                                    <input type="date" name="delivery_date"
                                        class="form-control <?php $__errorArgs = ['delivery_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('delivery_date', date('Y-m-d'))); ?>" max="<?php echo e(date('Y-m-d')); ?>"
                                        required>
                                    <?php $__errorArgs = ['delivery_date'];
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

                                <!-- Quantity -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quantity Delivered <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="quantity_delivered"
                                        class="form-control <?php $__errorArgs = ['quantity_delivered'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        step="0.01" min="0.01" x-bind:max="itemRemaining"
                                        x-model.number="quantityDelivered" @input="
        if (quantityDelivered > itemRemaining) {
            quantityDelivered = itemRemaining
        }
    " placeholder="0.00" required>
                                    <?php $__errorArgs = ['quantity_delivered'];
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

                                <!-- Document Numbers -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Waybill Number</label>
                                    <input type="text" name="waybill_number"
                                        class="form-control <?php $__errorArgs = ['waybill_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('waybill_number')); ?>" placeholder="WB-XXXXXX">
                                    <?php $__errorArgs = ['waybill_number'];
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

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Invoice Number</label>
                                    <input type="text" name="invoice_number"
                                        class="form-control <?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('invoice_number')); ?>" placeholder="INV-XXXXXX">
                                    <?php $__errorArgs = ['invoice_number'];
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

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Invoice Amount (₦)</label>
                                    <input type="number" name="invoice_amount"
                                        class="form-control <?php $__errorArgs = ['invoice_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('invoice_amount')); ?>" step="0.01" min="0" placeholder="0.00">
                                    <?php $__errorArgs = ['invoice_amount'];
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

                                <!-- Quality Notes -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Quality Notes</label>
                                    <textarea name="quality_notes"
                                        class="form-control <?php $__errorArgs = ['quality_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"
                                        placeholder="Notes about quality, condition, packaging, etc."><?php echo e(old('quality_notes')); ?></textarea>
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

                                <!-- Attachments -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Attachments (Photos, Documents)</label>
                                    <input type="file" name="attachments[]"
                                        class="form-control <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" multiple
                                        accept="image/jpeg,image/png,image/jpg,application/pdf">
                                    <small class="text-muted">Max 5MB per file. Accepted: JPEG, PNG, PDF</small>
                                    <?php $__errorArgs = ['attachments.*'];
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
                            </div>

                            <!-- Action buttons -->
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?php echo e(route('deliveries.index', $request)); ?>" class="btn btn-light">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary"
                                    :disabled="!selectedItemId || quantityDelivered <= 0 || quantityDelivered > itemRemaining">
                                    Record Delivery
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deliveryForm() {
    return {
        selectedItemId: '<?php echo e(old("request_item_id")); ?>',
        itemQuantity: 0,
        itemDelivered: 0,
        itemRemaining: 0,
        itemUnit: '',
        quantityDelivered: null,

        init() {
            if (this.selectedItemId) {
                this.updateItemDetails();
            }
        },

        updateItemDetails() {
            if (!this.selectedItemId) {
                return;
            }

            const select = this.$root.querySelector('select[name="request_item_id"]');
            const option = select.querySelector(`option[value="${this.selectedItemId}"]`);

            if (option) {
                this.itemQuantity = parseFloat(option.dataset.quantity).toFixed(2);
                this.itemDelivered = parseFloat(option.dataset.delivered).toFixed(2);
                this.itemRemaining = parseFloat(option.dataset.remaining);
                this.itemUnit = option.dataset.unit;
            }
        }
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/archillery/resources/views/deliveries/create.blade.php ENDPATH**/ ?>