<?php $__env->startSection('title', 'Create Procurement Request | PPMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Create Procurement Request</h4>
                    <p class="mb-0">Add materials to your cart and submit request</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('requests.index')); ?>">Requests</a></li>
                    <li class="breadcrumb-item active">Create</li>
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

        <div class="row" x-data="requestCart(<?php echo e($selectedProjectId ?? 'null'); ?>)">
            <div class="col-lg-8">
                <!-- Request Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Request Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Project <span class="text-danger">*</span></label>
                                <select x-model="projectId" class="form-select" required>
                                    <option value="">Select Project</option>
                                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($proj->id); ?>"
                                        <?php echo e($selectedProjectId == $proj->id ? 'selected' : ''); ?>>
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
                        <h4 class="card-title">Add Materials</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 col-md-8 mb-2 mb-md-0">
                                <!-- Enhanced search input with clear button -->
                                <div class="position-relative">
                                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted"
                                        style="left: 12px;"></i>
                                    <input type="text" x-model="searchQuery" @input.debounce.300ms="searchMaterials()"
                                        class="form-control form-control-mobile ps-5"
                                        placeholder="Search materials by name or code...">
                                    <button type="button" x-show="searchQuery"
                                        @click="searchQuery = ''; searchResults = []"
                                        class="btn btn-link position-absolute top-50 translate-middle-y p-0 text-muted"
                                        style="right: 12px;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Desktop: Standard dropdown -->
                            <div class="col-md-4 d-none d-md-block">
                                <select x-model="selectedCategory" @change="searchMaterials()"
                                    class="form-select form-control-mobile">
                                    <option value="">All Categories</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat); ?>"><?php echo e($cat); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <!-- Mobile: Horizontal scrollable category chips -->
                        <div class="d-md-none mb-3">
                            <label class="small text-muted mb-2 d-block">Filter by Category:</label>
                            <div class="category-chips-container">
                                <div class="category-chips">
                                    <button type="button"
                                        :class="['btn btn-sm category-chip', selectedCategory === '' ? 'btn-primary' : 'btn-outline-secondary']"
                                        @click="selectedCategory = ''; searchMaterials()">
                                        All
                                    </button>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button type="button"
                                        :class="['btn btn-sm category-chip', selectedCategory === '<?php echo e($cat); ?>' ? 'btn-primary' : 'btn-outline-secondary']"
                                        @click="selectedCategory = '<?php echo e($cat); ?>'; searchMaterials()">
                                        <?php echo e($cat); ?>

                                    </button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
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
                            <h5 class="mb-3">Cart Items (<span x-text="itemCount"></span>)</h5>

                            <!-- Desktop: Table view -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Category</th>
                                            <th style="width: 120px;">Quantity</th>
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
                                                        class="form-control form-control-sm" min="0.01" step="1">
                                                    <small class="text-muted" x-text="item.unit_of_measurement"></small>
                                                </td>
                                                <td>
                                                    <textarea x-model="item.remarks"
                                                        class="form-control form-control-sm" rows="1"
                                                        placeholder="Optional notes" @input="persistCart()"></textarea>
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

                            <!-- Mobile: Card-based view -->
                            <div class="d-md-none">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="card cart-item-card mb-3">
                                        <div class="card-body p-3">
                                            <!-- Header: Material name and delete button -->
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1 me-2">
                                                    <h6 class="mb-1 fw-bold" x-text="item.material_name"></h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <small class="text-muted" x-text="item.material_code"></small>
                                                        <span class="badge badge-secondary badge-sm"
                                                            x-text="item.category || 'N/A'"></span>
                                                    </div>
                                                </div>
                                                <button type="button" @click="removeItem(index)"
                                                    class="btn btn-sm btn-outline-danger touch-friendly-btn"
                                                    title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Quantity controls -->
                                            <div
                                                class="d-flex align-items-center justify-content-between mb-3 py-2 border-top border-bottom">
                                                <span class="text-muted">Quantity:</span>
                                                <div class="d-flex align-items-center">
                                                    <button type="button"
                                                        @click="updateQuantity(index, Math.max(0.01, item.quantity - 1))"
                                                        class="btn btn-outline-secondary quantity-btn">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number" :value="item.quantity"
                                                        @input="updateQuantity(index, $event.target.value)"
                                                        class="form-control form-control-sm text-center quantity-input mx-2"
                                                        min="0.01" step="1">
                                                    <button type="button"
                                                        @click="updateQuantity(index, item.quantity + 1)"
                                                        class="btn btn-outline-secondary quantity-btn">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                    <small class="ms-2 text-muted"
                                                        x-text="item.unit_of_measurement"></small>
                                                </div>
                                            </div>

                                            <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                                            <!-- Pricing section -->
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="small text-muted mb-1">Est. Unit Price (₦)</label>
                                                    <input type="number" :value="item.estimated_unit_price"
                                                        @input="updateEstimatedPrice(index, $event.target.value)"
                                                        class="form-control form-control-mobile" min="0" step="1"
                                                        placeholder="Optional">
                                                </div>
                                                <div class="col-6">
                                                    <label class="small text-muted mb-1">Est. Total</label>
                                                    <div class="form-control form-control-mobile bg-light fw-bold"
                                                        x-text="formatCurrency(item.estimated_total)"></div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- Remarks -->
                                            <div>
                                                <label class="small text-muted mb-1">Remarks</label>
                                                <textarea x-model="item.remarks"
                                                    class="form-control form-control-mobile" rows="2"
                                                    placeholder="Optional notes..." @input="persistCart()"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="isEmpty" class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted"></i>
                            <h5 class="mt-3">Cart is Empty</h5>
                            <p class="text-muted">Search and add materials to your cart</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card" x-data="{ sticky: false }">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0 text-white">Cart Summary</h4>
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
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" @click="submitRequest()"
                                :disabled="isEmpty || !projectId || !justification" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Submit Request
                            </button>
                            <button type="button" @click="clearCart()" :disabled="isEmpty"
                                class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Clear Cart
                            </button>
                        </div>

                        <div class="alert alert-info mt-3 mb-0" x-show="projectId">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                Cart is auto-saved for this project
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile: Sticky bottom bar for quick cart actions -->
            <div class="mobile-sticky-cart d-md-none" x-show="!isEmpty" x-cloak>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="cart-summary-mini">
                        <span class="fw-bold" x-text="itemCount + ' item' + (itemCount !== 1 ? 's' : '')"></span>
                        <?php if(auth()->user()->hasPermission('view-request-pricing')): ?>
                        <span class="text-success ms-2" x-text="formatCurrency(total)"></span>
                        <?php endif; ?>
                    </div>
                    <button type="button" @click="submitRequest()" :disabled="!projectId || !justification"
                        class="btn btn-primary btn-mobile-submit">
                        <i class="bi bi-send me-1"></i> Submit
                    </button>
                </div>
                <div x-show="!projectId || !justification" class="text-center mt-2">
                    <small class="text-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <span x-show="!projectId">Select a project</span>
                        <span x-show="projectId && !justification">Add justification</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mobile-first touch-friendly styles */
    @media (max-width: 767.98px) {

        /* Larger touch targets for form controls */
        .form-control-mobile,
        .form-select.form-control-mobile {
            min-height: 48px;
            font-size: 16px;
            /* Prevents iOS zoom on focus */
            padding: 12px 16px;
        }

        /* Category chips container */
        .category-chips-container {
            margin: 0 -15px;
            padding: 0 15px;
        }

        .category-chips {
            display: flex;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding-bottom: 8px;
            gap: 8px;
        }

        .category-chips::-webkit-scrollbar {
            display: none;
        }

        .category-chip {
            flex-shrink: 0;
            min-height: 40px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            white-space: nowrap;
        }

        /* Cart item cards */
        .cart-item-card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        /* Quantity controls */
        .quantity-btn {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 18px;
        }

        .quantity-input {
            width: 60px;
            height: 44px;
            font-size: 16px;
            font-weight: bold;
        }

        /* Touch-friendly buttons */
        .touch-friendly-btn {
            min-width: 44px;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Search results - larger touch targets */
        .list-group-item {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        /* Sticky bottom cart bar */
        .mobile-sticky-cart {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #dee2e6;
            padding: 12px 16px;
            padding-bottom: calc(12px + env(safe-area-inset-bottom, 0px));
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1050;
        }

        .cart-summary-mini {
            font-size: 14px;
        }

        .btn-mobile-submit {
            min-height: 48px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
        }

        /* Add padding at the bottom of the content to prevent overlap with sticky bar */
        .content-body {
            padding-bottom: 100px;
        }

        /* Badge adjustments */
        .badge-sm {
            font-size: 11px;
            padding: 4px 8px;
        }

        /* Card header adjustments */
        .card-header h4 {
            font-size: 1.1rem;
        }

        /* Form labels */
        .form-label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
    }

    /* Alpine.js cloak for preventing FOUC */
    [x-cloak] {
        display: none !important;
    }

    /* Smooth transitions */
    .category-chip {
        transition: all 0.2s ease;
    }

    .cart-item-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    @media (max-width: 767.98px) {
        .cart-item-card:active {
            transform: scale(0.98);
        }
    }
</style>

<script>
    function requestCart(preselectedProjectId) {
        return {
            projectId: preselectedProjectId || '',
            requiredByDate: '',
            justification: '',
            items: [],
            total: 0,
            searchQuery: '',
            selectedCategory: '',
            searchResults: [],
            searching: false,

            init() {
                // Set project ID from cart component
                this.$watch('projectId', (value) => {
                    if (value) {
                        const cartComponent = Alpine.$data(document.querySelector('[x-data*="cart"]'));
                        if (cartComponent) {
                            cartComponent.projectId = value;
                            this.loadCartItems();
                        }
                    }
                });

                if (this.projectId) {
                    this.loadCartItems();
                }
            },

            get isEmpty() {
                return this.items.length === 0;
            },

            get itemCount() {
                return this.items.length;
            },

            loadCartItems() {
                const stored = localStorage.getItem(`cart_project_${this.projectId}`);
                if (stored) {
                    const data = JSON.parse(stored);
                    this.items = data.items || [];
                    this.calculateTotal();
                }
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
                    this.persistCart();
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
                    this.persistCart();
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
                this.persistCart();
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
                this.persistCart();
            },

            calculateTotal() {
                this.total = this.items.reduce((sum, item) => {
                    return sum + (item.quantity * item.estimated_unit_price);
                }, 0);
            },

            clearCart() {
                if (confirm('Are you sure you want to clear the cart?')) {
                    this.items = [];
                    this.total = 0;
                    this.persistCart();
                    this.showNotification('Cart cleared', 'info');
                }
            },

            persistCart() {
                if (this.projectId) {
                    localStorage.setItem(`cart_project_${this.projectId}`, JSON.stringify({
                        items: this.items,
                        total: this.total,
                        timestamp: new Date().toISOString()
                    }));
                }
            },

            async submitRequest() {
                if (this.isEmpty || !this.projectId || !this.justification) {
                    this.showNotification('Please fill all required fields and add at least one item', 'error');
                    return;
                }

                const formData = {
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

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch('<?php echo e(route("requests.store")); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    if (response.ok) {
                        const result = await response.json();
                        // Clear cart
                        localStorage.removeItem(`cart_project_${this.projectId}`);
                        // Redirect to request show page
                        window.location.href = result.redirect_url || '<?php echo e(route("requests.index")); ?>';
                    } else {
                        const error = await response.json();
                        this.showNotification(error.message || 'Failed to submit request', 'error');
                    }
                } catch (error) {
                    console.error('Submit error:', error);
                    this.showNotification('Failed to submit request. Please try again.', 'error');
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Ahmadx/Downloads/ppms/resources/views/requests/create.blade.php ENDPATH**/ ?>