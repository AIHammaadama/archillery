<?php $__env->startSection('title', 'Edit Procurement Request | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Procurement Request</h4>
                    <p class="mb-0">Modify materials and request details</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('requests.index')); ?>">Requests</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?php echo e(route('requests.show', $request)); ?>"><?php echo e($request->request_number); ?></a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>

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

        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row" x-data="requestEditCart(<?php echo e($request->id); ?>)">
            <div class="col-lg-8">
                <!-- Request Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Request Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Request #<?php echo e($request->request_number); ?></strong> - Only draft requests can be edited
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Project <span class="text-danger">*</span></label>
                                <select x-model="projectId" class="form-select" required>
                                    <option value="">Select Project</option>
                                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($proj->id); ?>"
                                        <?php echo e($request->project_id == $proj->id ? 'selected' : ''); ?>>
                                        <?php echo e($proj->name); ?> (<?php echo e($proj->code); ?>)
                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Required By Date</label>
                                <input type="date" x-model="requiredByDate" class="form-control"
                                    min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Justification <span class="text-danger">*</span></label>
                                <textarea x-model="justification" class="form-control" rows="3"
                                    placeholder="Explain why these materials are needed..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Material Selection -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add/Edit Materials</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" x-model="searchQuery" @input.debounce.300ms="searchMaterials()"
                                        class="form-control" placeholder="Search materials by name or code...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select x-model="selectedCategory" @change="searchMaterials()" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat); ?>"><?php echo e($cat); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <!-- Search Results -->
                        <div x-show="searchResults.length > 0" class="mb-3">
                            <label class="small text-muted mb-2">Search Results (click to add):</label>
                            <div class="list-group">
                                <template x-for="material in searchResults" :key="material.id">
                                    <a href="javascript:void(0)" @click="addMaterialToCart(material)"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1" x-text="material.name"></h6>
                                                <p class="mb-1 small text-muted">
                                                    <span x-text="material.code"></span>
                                                    <span x-show="material.category"> | <span
                                                            x-text="material.category"></span></span>
                                                </p>
                                            </div>
                                            <span class="badge badge-primary">
                                                <span x-text="material.unit_of_measurement || 'units'"></span>
                                            </span>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <div x-show="searching" class="text-center py-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Searching...</span>
                            </div>
                            <span class="ms-2 text-muted">Searching materials...</span>
                        </div>

                        <!-- Cart Items -->
                        <div x-show="!isEmpty">
                            <hr class="my-4">
                            <h5 class="mb-3">Request Items (<span x-text="itemCount"></span>)</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Category</th>
                                            <th style="width: 120px;">Quantity</th>
                                            <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                            <th style="width: 150px;">Est. Unit Price (₦)</th>
                                            <th style="width: 150px;">Est. Total (₦)</th>
                                            <?php endif; ?>
                                            <th style="width: 150px;">Remarks</th>
                                            <th style="width: 60px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td>
                                                    <div class="fw-bold" x-text="item.material_name"></div>
                                                    <small class="text-muted" x-text="item.material_code"></small>
                                                </td>
                                                <td><span class="badge badge-secondary"
                                                        x-text="item.category || 'N/A'"></span></td>
                                                <td>
                                                    <input type="number" :value="item.quantity"
                                                        @input="updateQuantity(index, $event.target.value)"
                                                        class="form-control form-control-sm" min="0.01" step="0.01">
                                                    <small class="text-muted" x-text="item.unit_of_measurement"></small>
                                                </td>
                                                <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                                <td>
                                                    <input type="number" :value="item.estimated_unit_price"
                                                        @input="updateEstimatedPrice(index, $event.target.value)"
                                                        class="form-control form-control-sm" min="0" step="0.01"
                                                        placeholder="Optional">
                                                </td>
                                                <td class="fw-bold" x-text="formatCurrency(item.estimated_total)"></td>
                                                <?php endif; ?>
                                                <td>
                                                    <textarea
                                                        x-model="item.remarks"
                                                        class="form-control form-control-sm"
                                                        rows="1"
                                                        placeholder="Optional notes"></textarea>
                                                </td>
                                                <td>
                                                    <button type="button" @click="removeItem(index)"
                                                        class="btn btn-sm btn-danger" title="Remove">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div x-show="isEmpty" class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted"></i>
                            <h5 class="mt-3">No Items</h5>
                            <p class="text-muted">Search and add materials to your request</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0 text-white">Request Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Items:</span>
                                <span class="fw-bold" x-text="itemCount"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Quantity:</span>
                                <span class="fw-bold"
                                    x-text="items.reduce((sum, item) => sum + parseFloat(item.quantity), 0).toFixed(2)"></span>
                            </div>
                            <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Estimated Total:</span>
                                <span class="fw-bold text-success fs-5" x-text="formatCurrency(total)"></span>
                            </div>
                            <small class="text-muted">Note: Final pricing will be determined by procurement</small>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" @click="updateRequest()"
                                :disabled="isEmpty || !projectId || !justification || saving" class="btn btn-primary">
                                <span x-show="!saving"><i class="bi bi-save me-1"></i> Update Request</span>
                                <span x-show="saving">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Updating...
                                </span>
                            </button>
                            <a href="<?php echo e(route('requests.show', $request)); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                        </div>

                        <div class="alert alert-warning mt-3 mb-0">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                Changes will update the existing request
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$existingItems = $request->items
->map(function ($item) {
    $quantity = $item->quantity;
    $price = $item->estimated_unit_price ?? 0;
    return [
        'material_id' => $item->material_id,
        'material_code' => optional($item->material)->code,
        'material_name' => optional($item->material)->name,
        'category' => optional($item->material)->category,
        'unit_of_measurement' => optional($item->material)->unit_of_measurement,
        'quantity' => $quantity,
        'estimated_unit_price' => $price,
        'estimated_total' => $quantity * $price,
        'remarks' => $item->remarks ?? '',
    ];
})
->values();
?>
<script>
function requestEditCart(requestId) {
    return {
        requestId: requestId,
        projectId: '<?php echo e($request->project_id); ?>',
        requiredByDate: '<?php echo e($request->required_by_date ? $request->required_by_date->format("Y-m-d") : ""); ?>',
        justification: `<?php echo e(str_replace(['`
        ', "\n", "\r"], ['\\
        `', ' ', ' '], $request->justification)); ?>`,
        items: [],
        total: 0,
        searchQuery: '',
        selectedCategory: '',
        searchResults: [],
        searching: false,
        saving: false,

        init() {
            // Load existing request items
            this.loadExistingItems();
        },

        loadExistingItems() {
            this.items = <?php echo json_encode($existingItems, 15, 512) ?>;
            this.calculateTotal();
        },

        get isEmpty() {
            return this.items.length === 0;
        },

        get itemCount() {
            return this.items.length;
        },

        async searchMaterials() {
            if (!this.searchQuery && !this.selectedCategory) {
                this.searchResults = [];
                return;
            }

            this.searching = true;
            try {
                const params = new URLSearchParams();
                if (this.searchQuery) params.append('q', this.searchQuery);
                if (this.selectedCategory) params.append('category', this.selectedCategory);

                const response = await fetch(`/api/materials/search?${params}`);
                if (response.ok) {
                    this.searchResults = await response.json();
                }
            } catch (error) {
                console.error('Failed to search materials:', error);
            } finally {
                this.searching = false;
            }
        },

        addMaterialToCart(material) {
            const existingItem = this.items.find(item => item.material_id === material.id);

            if (existingItem) {
                existingItem.quantity++;
                this.calculateTotal();
                this.showNotification('Quantity increased for ' + material.name, 'info');
            } else {
                this.items.push({
                    material_id: material.id,
                    material_code: material.code,
                    material_name: material.name,
                    category: material.category,
                    unit_of_measurement: material.unit_of_measurement,
                    quantity: 1,
                    estimated_unit_price: 0,
                    estimated_total: 0,
                    remarks: ''
                });
                this.calculateTotal();
                this.showNotification('Added ' + material.name + ' to cart', 'success');
            }

            // Clear search
            this.searchQuery = '';
            this.searchResults = [];
        },

        removeItem(index) {
            const item = this.items[index];
            this.items.splice(index, 1);
            this.calculateTotal();
            this.showNotification('Removed ' + item.material_name, 'info');
        },

        updateQuantity(index, quantity) {
            const qty = parseFloat(quantity);
            if (qty < 0.01) {
                this.removeItem(index);
                return;
            }
            this.items[index].quantity = qty;
            this.calculateItemTotal(index);
        },

        updateEstimatedPrice(index, price) {
            this.items[index].estimated_unit_price = parseFloat(price) || 0;
            this.calculateItemTotal(index);
        },

        calculateItemTotal(index) {
            const item = this.items[index];
            item.estimated_total = item.quantity * item.estimated_unit_price;
            this.calculateTotal();
        },

        calculateTotal() {
            this.total = this.items.reduce((sum, item) => {
                return sum + (item.quantity * item.estimated_unit_price);
            }, 0);
        },

        async updateRequest() {
            if (this.isEmpty || !this.projectId || !this.justification) {
                this.showNotification('Please fill all required fields and add at least one item', 'error');
                return;
            }

            if (this.saving) return;

            const formData = {
                _method: 'PUT',
                project_id: this.projectId,
                required_by_date: this.requiredByDate || null,
                justification: this.justification,
                items: this.items.map(item => ({
                    material_id: item.material_id,
                    quantity: item.quantity,
                    estimated_unit_price: item.estimated_unit_price,
                    remarks: item.remarks
                }))
            };

            this.saving = true;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('<?php echo e(route("requests.update", $request)); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    this.showNotification('Request updated successfully', 'success');
                    setTimeout(() => {
                        window.location.href = '<?php echo e(route("requests.show", $request)); ?>';
                    }, 1000);
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Failed to update request', 'error');
                }
            } catch (error) {
                console.error('Update error:', error);
                this.showNotification('Failed to update request. Please try again.', 'error');
            } finally {
                this.saving = false;
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN',
                minimumFractionDigits: 2
            }).format(amount);
        },

        showNotification(message, type) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    message,
                    type
                }
            }));
        }
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/requests/edit.blade.php ENDPATH**/ ?>