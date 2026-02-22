@extends('layouts.admin')

@section('title', $request->request_number . ' | PPMS')
@php
$RequestStatus = App\Enums\RequestStatus::class;
@endphp
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ $request->request_number }}</h4>
                    <p class="mb-0">Procurement Request Details</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('requests.index') }}">Requests</a></li>
                    <li class="breadcrumb-item active">{{ $request->request_number }}</li>
                </ol>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row">
            <!-- Request Details -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Request Information</h4>
                        @if($request->isEditable())
                        @can('update', $request)
                        <a href="{{ route('requests.edit', $request) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @endcan
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                <span class="badge badge-{{ $request->status->badgeClass() }} fs-6">
                                    {{ $request->status->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Project</label>
                            <div>
                                <a href="{{ route('projects.show', $request->project) }}" class="fw-bold text-primary">
                                    {{ $request->project->name }}
                                </a>
                                <p class="mb-0 small text-muted">{{ $request->project->code }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Requested By</label>
                            <p class="mb-0">
                                @if($request->requestedBy)
                                {{ $request->requestedBy->firstname }} {{ $request->requestedBy->lastname }}<br>
                                <small class="text-muted">{{ $request->request_date->format('M d, Y') }}</small>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>

                        @if($request->required_by_date)
                        <div class="mb-3">
                            <label class="small text-muted">Required By</label>
                            <p class="mb-0">{{ $request->required_by_date->format('M d, Y') }}</p>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="small text-muted">Justification</label>
                            <p class="mb-0">{{ $request->justification }}</p>
                        </div>

                        @if($canViewPricing)
                        <hr>

                        @if($request->total_quoted_amount > 0)
                        <div class="mb-0">
                            <label class="small text-muted">Quoted Total</label>
                            <p class="mb-0 fw-bold text-primary fs-5">
                                ₦{{ number_format($request->total_quoted_amount, 2) }}
                            </p>
                        </div>
                        @endif
                        @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>Pricing information is not visible for your role.</small>
                        </div>
                        @endif

                        @if($request->isEditable())
                        <hr>
                        <form action="{{ route('requests.submit', $request) }}" method="POST"
                            onsubmit="return confirm('Submit this request for processing?');">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send me-1"></i> Submit Request
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Status Timeline</h4>
                    </div>
                    <div class="card-body">
                        @if($request->statusHistory->count() > 0)
                        <div class="timeline">
                            @foreach($request->statusHistory as $history)
                            <div class="timeline-item">
                                <div
                                    class="timeline-marker bg-{{ $history->to_status === 'approved' ? 'success' : ($history->to_status === 'rejected' ? 'danger' : 'primary') }}">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</h6>
                                    <p class="mb-1 small">
                                        By {{ $history->changedBy->firstname }} {{ $history->changedBy->lastname }}
                                    </p>
                                    @if($history->comments)
                                    <p class="mb-1 small text-muted">{{ $history->comments }}</p>
                                    @endif
                                    <small class="text-muted">
                                        {{ $history->created_at?->format('M d, Y g:i A') ?? '—' }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted mb-0">No status changes yet</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Request Items -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Requested Items ({{ $request->items->count() }})</h4>
                        @if(
                        auth()->user()->hasPermission('process-purchase-request') &&
                        $request->status === $RequestStatus::PENDING_DIRECTOR
                        )
                        <a href="{{ route('approvals.edit-assignment', $request->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i> Edit Assignment
                        </a>
                        @endif
                        @if($request->items)
                        <a href="{{ route('reports.request-detail', ['request' => $request, 'download' => '1']) }}"
                            class="btn btn-sm btn-info me-2" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> Download PDF
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Quantity</th>
                                        @if($canViewPricing)
                                        <th>Vendor</th>
                                        <th>Quoted Price</th>
                                        <th>Quoted Total</th>
                                        @endif
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $item->material->name }}</div>
                                            <small class="text-muted">{{ $item->material->code }}</small>
                                        </td>
                                        <td>{{ number_format($item->quantity, 2) }}
                                            {{ $item->material->unit_of_measurement }}
                                        </td>
                                        @if($canViewPricing)
                                        <!-- <td>₦{{ number_format($item->estimated_unit_price, 2) }}</td>
                                        <td class="fw-bold">₦{{ number_format($item->estimated_total, 2) }}</td> -->
                                        <td>
                                            @if($item->vendor)
                                            {{ $item->vendor->name }}
                                            @else
                                            <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->quoted_unit_price)
                                            ₦{{ number_format($item->quoted_unit_price, 2) }}
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-primary">
                                            @if($item->quoted_total)
                                            ₦{{ number_format($item->quoted_total, 2) }}
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            @if($item->remarks)
                                            <small>{{ $item->remarks }}</small>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->delivery_status === 'complete')
                                            <span class="badge bg-success">Complete</span>
                                            @elseif($item->delivery_status === 'partial')
                                            <span class="badge bg-warning">Partial</span>
                                            @else
                                            <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(
                                            auth()->user()->hasPermission('record-deliveries') &&
                                            $item->delivery_status !== 'complete'
                                            )
                                            <a href="{{ route('deliveries.create', $request) }}"
                                                class="btn btn-sm btn-primary" title="Record Delivery">
                                                <i class="bi bi-plus-circle"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Receipts -->
                <div class="card mt-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Payment Receipts</h4>
                        @if(auth()->user()->can('update', $request) || auth()->user()->hasPermission('process-purchase-request'))
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadReceiptModal">
                            <i class="bi bi-upload me-1"></i> Upload Receipt
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($request->paymentReceipts && $request->paymentReceipts->count() > 0)
                        <div class="row g-3">
                            @foreach($request->paymentReceipts as $receipt)
                            <div class="col-md-4 mb-3">
                                <div class="card border h-100">
                                    @if(preg_match('/\.(jpg|jpeg|png)$/i', $receipt->file_path))
                                    <div style="height: 120px; overflow: hidden; background-color: #f8f9fa;"
                                        class="d-flex align-items-center justify-content-center">
                                        <a href="{{ route('receipts.download', $receipt) }}" target="_blank">
                                            <i class="bi bi-image text-primary" style="font-size: 3rem;"></i>
                                        </a>
                                    </div>
                                    @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                        style="height: 120px;">
                                        <a href="{{ route('receipts.download', $receipt) }}" target="_blank">
                                            <i class="bi bi-file-pdf text-danger" style="font-size: 3rem;"></i>
                                        </a>
                                    </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <p class="mb-1 small text-truncate fw-bold" title="{{ $receipt->original_filename }}">
                                            {{ $receipt->original_filename }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted d-block text-truncate" style="max-width: 120px;" title="{{ $receipt->vendor ? $receipt->vendor->name : 'No vendor' }}">
                                                <i class="bi bi-shop me-1"></i>{{ $receipt->vendor ? $receipt->vendor->name : 'No vendor' }}
                                            </small>
                                            <a href="{{ route('receipts.download', $receipt) }}" target="_blank"
                                                class="btn btn-xs btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center text-muted small py-3">No receipts uploaded yet</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Receipt Modal -->
@if(auth()->user()->can('update', $request) || auth()->user()->hasPermission('process-purchase-request'))
<div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('requests.receipts.store', $request) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Payment Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">Select Vendor...</option>
                            @php
                                $requestVendors = collect();
                                if($request->items) {
                                    $requestVendors = $request->items->pluck('vendor')->filter()->unique('id');
                                }
                            @endphp
                            @foreach($requestVendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Select the vendor this payment was made to.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receipt Files <span class="text-danger">*</span></label>
                        <input type="file" name="receipts[]" class="form-control" multiple accept=".pdf,.jpeg,.png,.jpg" required>
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
@endif

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
@endsection