@extends('layouts.admin')

@section('title', 'Assign Materials - ' . $vendor->name . ' | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Assign Materials to {{ $vendor->name }}</h4>
                    <p class="mb-0">Set pricing and terms for materials</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendors</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendors.show', $vendor) }}">{{ $vendor->name }}</a>
                    </li>
                    <li class="breadcrumb-item active">Assign Materials</li>
                </ol>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('vendors.materials.store', $vendor) }}" method="POST" x-data="materialAssignment()">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Material Assignments</h4>
                            <button type="button" @click="addMaterial()" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Add Material
                            </button>
                        </div>
                        <div class="card-body">
                            <template x-if="assignments.length === 0">
                                <div class="text-center py-5">
                                    <i class="bi bi-box-seam display-1 text-muted"></i>
                                    <h5 class="mt-3">No Materials Assigned</h5>
                                    <p class="text-muted">Click "Add Material" to assign materials to this vendor</p>
                                </div>
                            </template>

                            <!-- Removed table-responsive to allow dropdown overflow -->
                            <div x-show="assignments.length > 0">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th style="width: 150px;">Price (₦) <span class="text-danger">*</span></th>
                                            <th style="width: 150px;">Min. Order Qty</th>
                                            <!-- <th style="width: 20px;">Lead Time (days)</th>
                                            <th style="width: 80px;">Valid Until</th> -->
                                            <th style="width: 60px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(assignment, index) in assignments" :key="index">
                                            <tr>
                                                <td>
                                                    <div class="position-relative" x-data="materialSearch(assignment)">
                                                        <input type="hidden"
                                                            :name="'materials[' + index + '][material_id]'"
                                                            x-model="assignment.material_id">
                                                        
                                                        <div class="input-group input-group-sm">
                                                            <select class="form-select form-select-sm" 
                                                                    style="max-width: 120px;"
                                                                    x-model="selectedCategory"
                                                                    @change="search = ''; assignment.material_id = ''">
                                                                <option value="">All Categories</option>
                                                                <template x-for="cat in window.materialCategories" :key="cat">
                                                                    <option :value="cat" x-text="cat"></option>
                                                                </template>
                                                            </select>
                                                            <input type="text" class="form-control form-control-sm"
                                                                x-model="search" @focus="open = true"
                                                                @click.outside="open = false"
                                                                placeholder="Search material..." autocomplete="off">
                                                        </div>

                                                        <div x-show="open"
                                                            class="list-group position-absolute w-100 mt-1 shadow-sm"
                                                            style="max-height: 200px; overflow-y: auto; z-index: 1000;"
                                                            x-transition>
                                                            <template x-for="m in filtered" :key="m.id">
                                                                <a href="#"
                                                                    class="list-group-item list-group-item-action py-1 px-2 small"
                                                                    @click.prevent="select(m)">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span class="fw-bold" x-text="m.name"></span>
                                                                        <span class="text-muted" x-text="m.code"></span>
                                                                    </div>
                                                                    <small class="text-muted d-block">
                                                                        <span x-text="m.category" class="badge bg-light text-dark me-1"></span>
                                                                        <span x-text="m.unit_of_measurement"></span>
                                                                    </small>
                                                                </a>
                                                            </template>
                                                            <div x-show="filtered.length === 0"
                                                                class="list-group-item text-muted small">
                                                                No matches found
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" :name="'materials[' + index + '][price]'"
                                                        x-model="assignment.price" class="form-control form-control-sm"
                                                        step="0.01" min="0" required placeholder="0.00">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        :name="'materials[' + index + '][minimum_order_quantity]'"
                                                        x-model="assignment.minimum_order_quantity"
                                                        class="form-control form-control-sm" step="0.01" min="0"
                                                        placeholder="Optional">
                                                </td>
                                                <!-- <td>
                                                    <input type="number"
                                                        :name="'materials[' + index + '][lead_time_days]'"
                                                        x-model="assignment.lead_time_days"
                                                        class="form-control form-control-sm" min="0"
                                                        placeholder="Optional">
                                                </td>
                                                <td>
                                                    <input type="date" :name="'materials[' + index + '][valid_until]'"
                                                        x-model="assignment.valid_until"
                                                        class="form-control form-control-sm"
                                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                                </td> -->
                                                <td class="text-center">
                                                    <button type="button" @click="removeMaterial(index)"
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
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0 text-white">Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="small text-muted">Vendor</label>
                                <p class="fw-bold mb-0">{{ $vendor->name }}</p>
                                <small class="text-muted">{{ $vendor->code }}</small>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted">Contact Person</label>
                                <p class="mb-0">{{ $vendor->contact_person }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted">Materials Assigned</label>
                                <p class="mb-0 fw-bold fs-4" x-text="assignments.length"></p>
                            </div>

                            <hr>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Save Assignments
                                </button>
                                <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                            </div>

                            <div class="alert alert-info mt-3 mb-0">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    Saving will replace all existing material assignments for this vendor
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    $vendorMaterials = $materials->map(fn($m) => [
        'id' => $m->id,
        'name' => $m->name,
        'code' => $m->code,
        'category' => $m->category,
        'unit_of_measurement' => $m->unit_of_measurement
    ]);
    
    // extract unique categories for the filter
    $categories = $materials->pluck('category')->unique()->values();

    $existingAssignments = $vendor->materials->map(fn($m) => [
        'material_id' => $m->id,
        'price' => $m->pivot->price,
        'minimum_order_quantity' => $m->pivot->minimum_order_quantity,
        'lead_time_days' => $m->pivot->lead_time_days,
        'valid_until' => $m->pivot->valid_until ? \Carbon\Carbon::parse($m->pivot->valid_until)->format('Y-m-d') : ''
    ]);
@endphp

<script>
    // Prepare data from PHP for JavaScript
    window.vendorMaterials = @json($vendorMaterials);
    window.materialCategories = @json($categories);
    window.existingAssignments = @json($existingAssignments);

    function materialAssignment() {
        return {
            assignments: [],

            init() {
                // Load existing assignments from global variable
                if (window.existingAssignments && window.existingAssignments.length > 0) {
                    this.assignments = JSON.parse(JSON.stringify(window.existingAssignments));
                }
            },

            addMaterial() {
                this.assignments.push({
                    material_id: '',
                    price: '',
                    minimum_order_quantity: '',
                    lead_time_days: '',
                    valid_until: ''
                });
            },

            removeMaterial(index) {
                if (confirm('Remove this material assignment?')) {
                    this.assignments.splice(index, 1);
                }
            }
        }
    }

    function materialSearch(assignment) {
        return {
            search: '',
            selectedCategory: '',
            open: false,
            
            init() {
                if (assignment.material_id) {
                    let m = window.vendorMaterials.find(m => m.id == assignment.material_id);
                    if (m) {
                        this.search = m.name + ' (' + m.code + ')';
                        this.selectedCategory = m.category || '';
                    }
                }
                
                // Watch for changes from outside (e.g. data load) or inside
                this.$watch(() => assignment.material_id, (id) => {
                    if (!id) {
                        this.search = '';
                        return;
                    }
                    let m = window.vendorMaterials.find(m => m.id == id);
                    if (m) {
                        this.search = m.name + ' (' + m.code + ')';
                        // Optionally update category, but maybe user wants to keep current category filter?
                        // Let's only update search text.
                    }
                });
            },

            get filtered() {
                let items = window.vendorMaterials;
                
                if (this.selectedCategory) {
                    items = items.filter(m => m.category === this.selectedCategory);
                }

                if (!this.search) return items;
                
                return items.filter(m =>
                    m.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    m.code.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            select(m) {
                // Directly update the assignment object reference
                assignment.material_id = m.id;
                this.search = m.name + ' (' + m.code + ')';
                this.open = false;
            }
        }
    }
</script>
@endsection