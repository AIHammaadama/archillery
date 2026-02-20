@extends('layouts.admin')

@section('title', 'Record Delivery | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Record Delivery</h4>
                    <p class="mb-0">{{ $request->request_number }}</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('requests.show', $request) }}">{{ $request->request_number }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('deliveries.index', $request) }}">Deliveries</a></li>
                    <li class="breadcrumb-item active">Record</li>
                </ol>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

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
                            <div class="fw-bold">{{ $request->project->name }}</div>
                        </div>
                        @if(auth()->user()->hasPermission('view-request-pricing'))
                        <div class="mb-3">
                            <label class="small text-muted">Total Quoted Amount</label>
                            <p class="mb-0 fw-bold text-success fs-5">
                                ₦{{ number_format($request->total_quoted_amount, 2) }}
                            </p>
                        </div>
                        @endif

                        <hr>

                        <h6 class="mb-3">Items Overview</h6>
                        @foreach($request->items as $item)
                        @php
                        $totalDelivered = $item->deliveries()
                        ->whereIn('verification_status', ['accepted', 'partial'])
                        ->sum('quantity_delivered');
                        $remaining = $item->quantity - $totalDelivered;
                        @endphp

                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fw-bold">{{ $item->material->name }}</div>
                            <small class="text-muted">{{ $item->material->code }}</small>
                            <div class="mt-2">
                                <small class="text-muted d-block">Ordered: {{ number_format($item->quantity, 2) }}
                                    {{ $item->material->unit_of_measurement }}</small>
                                <small class="text-muted d-block">Delivered: {{ number_format($totalDelivered, 2) }}
                                    {{ $item->material->unit_of_measurement }}</small>
                                <small class="d-block {{ $remaining > 0 ? 'text-warning' : 'text-success' }}">
                                    Remaining: {{ number_format($remaining, 2) }}
                                    {{ $item->material->unit_of_measurement }}
                                </small>
                            </div>
                        </div>
                        @endforeach
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
                        <form action="{{ route('deliveries.store', $request) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Item Selection -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Item <span class="text-danger">*</span></label>
                                    <select name="request_item_id"
                                        class="form-control @error('request_item_id') is-invalid @enderror"
                                        x-model="selectedItemId" @change="updateItemDetails()" required>
                                        <option value="">Select Item</option>
                                        @foreach($request->items as $item)
                                        @php
                                        $totalDelivered = $item->deliveries()
                                        ->whereIn('verification_status', ['accepted', 'partial'])
                                        ->sum('quantity_delivered');
                                        $remaining = $item->quantity - $totalDelivered;
                                        @endphp
                                        @if($remaining > 0)
                                        <option value="{{ $item->id }}" data-quantity="{{ $item->quantity }}"
                                            data-delivered="{{ $totalDelivered }}" data-remaining="{{ $remaining }}"
                                            data-unit="{{ $item->material->unit_of_measurement }}"
                                            {{ old('request_item_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->material->name }} ({{ number_format($remaining, 2) }}
                                            {{ $item->material->unit_of_measurement }} remaining)
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @error('request_item_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

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
                                        class="form-control @error('delivery_date') is-invalid @enderror"
                                        value="{{ old('delivery_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                                        required>
                                    @error('delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quantity Delivered <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="quantity_delivered"
                                        class="form-control @error('quantity_delivered') is-invalid @enderror"
                                        step="0.01" min="0.01" x-bind:max="itemRemaining"
                                        x-model.number="quantityDelivered" @input="
        if (quantityDelivered > itemRemaining) {
            quantityDelivered = itemRemaining
        }
    " placeholder="0.00" required>
                                    @error('quantity_delivered')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Document Numbers -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Waybill Number</label>
                                    <input type="text" name="waybill_number"
                                        class="form-control @error('waybill_number') is-invalid @enderror"
                                        value="{{ old('waybill_number') }}" placeholder="WB-XXXXXX">
                                    @error('waybill_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Invoice Number</label>
                                    <input type="text" name="invoice_number"
                                        class="form-control @error('invoice_number') is-invalid @enderror"
                                        value="{{ old('invoice_number') }}" placeholder="INV-XXXXXX">
                                    @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Invoice Amount (₦)</label>
                                    <input type="number" name="invoice_amount"
                                        class="form-control @error('invoice_amount') is-invalid @enderror"
                                        value="{{ old('invoice_amount') }}" step="0.01" min="0" placeholder="0.00">
                                    @error('invoice_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Quality Notes -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Quality Notes</label>
                                    <textarea name="quality_notes"
                                        class="form-control @error('quality_notes') is-invalid @enderror" rows="3"
                                        placeholder="Notes about quality, condition, packaging, etc.">{{ old('quality_notes') }}</textarea>
                                    @error('quality_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Attachments -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Attachments (Photos, Documents)</label>
                                    <input type="file" name="attachments[]"
                                        class="form-control @error('attachments.*') is-invalid @enderror" multiple
                                        accept="image/jpeg,image/png,image/jpg,application/pdf">
                                    <small class="text-muted">Max 5MB per file. Accepted: JPEG, PNG, PDF</small>
                                    @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action buttons -->
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('deliveries.index', $request) }}" class="btn btn-light">
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
        selectedItemId: '{{ old("request_item_id") }}',
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
@endsection