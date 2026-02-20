@extends('layouts.admin')

@section('title', 'Edit Vendor | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Vendor</h4>
                    <p class="mb-0">Update vendor information</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendors</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendors.show', $vendor) }}">{{ $vendor->name }}</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>

        <!-- Alerts -->
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

        <!-- Edit form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Vendor Information</h4>
                    </div>
                    <div class="card-body" x-data="vendorForm()">
                        <form action="{{ route('vendors.update', $vendor) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $vendor->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Registration Number</label>
                                    <input type="text" name="registration_number"
                                        class="form-control @error('registration_number') is-invalid @enderror"
                                        value="{{ old('registration_number', $vendor->business_registration) }}">
                                    @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Person</label>
                                    <input type="text" name="contact_person"
                                        class="form-control @error('contact_person') is-invalid @enderror"
                                        value="{{ old('contact_person', $vendor->contact_person) }}">
                                    @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $vendor->email) }}">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $vendor->phone) }}">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Alternative Phone</label>
                                    <input type="text" name="alt_phone"
                                        class="form-control @error('alt_phone') is-invalid @enderror"
                                        value="{{ old('alt_phone', $vendor->alt_phone) }}">
                                    @error('alt_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                        rows="2">{{ old('address', $vendor->address) }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Location -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State</label>
                                    <select name="state_id" class="form-select @error('state_id') is-invalid @enderror"
                                        x-model="selectedState" @change="loadLgas()">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', $vendor->state_id) == $state->id ? 'selected' : '' }}>
                                            {{ $state->state }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">LGA</label>
                                    <select name="lga_id" class="form-select @error('lga_id') is-invalid @enderror"
                                        x-model="selectedLga" :disabled="!selectedState || lgasLoading">
                                        <option value="">Select LGA</option>
                                        <template x-for="lga in lgas" :key="lga.id">
                                            <option :value="lga.id" x-text="lga.lga"
                                                :selected="lga.id == {{ old('lga_id', $vendor->lga_id ?? 'null') }}">
                                            </option>
                                        </template>
                                    </select>
                                    <div x-show="lgasLoading" class="small text-muted mt-1">
                                        <i class="bi bi-hourglass-split"></i> Loading LGAs...
                                    </div>
                                    @error('lga_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bank Information -->
                                <div class="col-md-12">
                                    <hr class="my-4">
                                    <h5 class="mb-3">Bank Information</h5>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name"
                                        class="form-control @error('bank_name') is-invalid @enderror"
                                        value="{{ old('bank_name', $vendor->bank_name) }}">
                                    @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_number"
                                        class="form-control @error('account_number') is-invalid @enderror"
                                        value="{{ old('account_number', $vendor->bank_account) }}">
                                    @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Account Name</label>
                                    <input type="text" name="bank_account_name"
                                        class="form-control @error('bank_account_name') is-invalid @enderror"
                                        value="{{ old('bank_account_name', $vendor->bank_account_name) }}">
                                    @error('bank_account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tax Id</label>
                                    <input type="text" name="tax_id"
                                        class="form-control @error('tax_id') is-invalid @enderror"
                                        value="{{ old('tax_id', $vendor->tax_id) }}">
                                    @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status & Rating -->
                                <div class="col-md-12">
                                    <hr class="my-4">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror"
                                        required>
                                        <option value="active"
                                            {{ old('status', $vendor->status) === 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $vendor->status) === 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                        <option value="suspended"
                                            {{ old('status', $vendor->status) === 'suspended' ? 'selected' : '' }}>
                                            Suspended</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Rating</label>
                                    <input type="number" name="rating"
                                        class="form-control @error('rating') is-invalid @enderror"
                                        value="{{ old('rating', $vendor->rating) }}" min="1" max="5" step="0.1">
                                    <small class="text-muted">Rating between 1.0 and 5.0</small>
                                    @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                        rows="3">{{ old('notes', $vendor->notes) }}</textarea>
                                    @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action buttons -->
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-light">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Update Vendor
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
    function vendorForm() {
        return {
            selectedState: '{{ old("state_id", $vendor->state_id) }}',
            selectedLga: '{{ old("lga_id", $vendor->lga_id) }}',
            lgas: @json($lgas ?? []),
            lgasLoading: false,

            init() {
                if (this.selectedState && this.lgas.length === 0) {
                    this.loadLgas();
                }
            },

            async loadLgas() {
                if (!this.selectedState) {
                    this.lgas = [];
                    this.selectedLga = '';
                    return;
                }

                this.lgasLoading = true;
                try {
                    const response = await fetch(`/api/states/${this.selectedState}/lgas`);
                    if (response.ok) {
                        this.lgas = await response.json();
                    }
                } catch (error) {
                    console.error('Failed to load LGAs:', error);
                } finally {
                    this.lgasLoading = false;
                }
            }
        }
    }
</script>
@endsection