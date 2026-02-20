@extends('layouts.admin')

@section('title', $delivery->delivery_number . ' | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ $delivery->delivery_number }}</h4>
                    <p class="mb-0">Delivery Details</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('requests.show', $delivery->request) }}">{{ $delivery->request->request_number }}</a>
                    </li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('deliveries.index', $delivery->request) }}">Deliveries</a></li>
                    <li class="breadcrumb-item active">{{ $delivery->delivery_number }}</li>
                </ol>
            </div>
        </div>

        <!-- Alerts -->
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
            <!-- Delivery Details -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Delivery Information</h4>
                        @if($delivery->verification_status === 'pending' &&
                        auth()->user()->hasPermission('verify-deliveries'))
                        <a href="{{ route('deliveries.verify', $delivery) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-check-circle"></i> Verify
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                @if($delivery->verification_status === 'accepted')
                                <span class="badge badge-success fs-6">Accepted</span>
                                @elseif($delivery->verification_status === 'rejected')
                                <span class="badge badge-danger fs-6">Rejected</span>
                                @elseif($delivery->verification_status === 'partial')
                                <span class="badge badge-warning fs-6">Partial</span>
                                @else
                                <span class="badge badge-secondary fs-6">Pending Verification</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Delivery Date</label>
                            <p class="mb-0">{{ $delivery->delivery_date->format('M d, Y') }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Quantity Delivered</label>
                            <p class="mb-0 fw-bold fs-5">
                                {{ number_format($delivery->quantity_delivered, 2) }}
                                {{ $delivery->requestItem->material->unit_of_measurement }}
                            </p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Material</label>
                            <div class="fw-bold">{{ $delivery->requestItem->material->name }}</div>
                            <small class="text-muted">{{ $delivery->requestItem->material->code }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Vendor</label>
                            <p class="mb-0">{{ $delivery->vendor ? $delivery->vendor->name : 'N/A' }}</p>
                        </div>

                        @if($delivery->waybill_number)
                        <hr>
                        <div class="mb-3">
                            <label class="small text-muted">Waybill Number</label>
                            <p class="mb-0">{{ $delivery->waybill_number }}</p>
                        </div>
                        @endif

                        @if($delivery->invoice_number)
                        <div class="mb-3">
                            <label class="small text-muted">Invoice Number</label>
                            <p class="mb-0">{{ $delivery->invoice_number }}</p>
                        </div>
                        @endif

                        @if($delivery->invoice_amount)
                        <div class="mb-3">
                            <label class="small text-muted">Invoice Amount</label>
                            <p class="mb-0 fw-bold text-success">₦{{ number_format($delivery->invoice_amount, 2) }}</p>
                        </div>
                        @endif

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Received By</label>
                            <p class="mb-0">
                                @if($delivery->receivedBy)
                                {{ $delivery->receivedBy->firstname }} {{ $delivery->receivedBy->lastname }}<br>
                                <small class="text-muted">{{ $delivery->created_at->format('M d, Y g:i A') }}</small>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>

                        @if($delivery->verifiedBy)
                        <div class="mb-0">
                            <label class="small text-muted">Verified By</label>
                            <p class="mb-0">
                                {{ $delivery->verifiedBy->firstname }} {{ $delivery->verifiedBy->lastname }}<br>
                                <small class="text-muted">{{ $delivery->updated_at->format('M d, Y g:i A') }}</small>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quality Notes & Attachments -->
            <div class="col-lg-8">
                <!-- Site Manager Feedback -->
                @if($delivery->siteManagerCanUpdate())
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Site Manager Feedback</h4>
                        @if(auth()->user()->hasRole('site_manager') &&
                        $delivery->request->project->site_manager_id === auth()->id())
                        <a href="{{ route('deliveries.update-status', $delivery) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i>
                            {{ $delivery->siteManagerHasUpdated() ? 'Update' : 'Add' }} Feedback
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($delivery->siteManagerHasUpdated())
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                @if($delivery->site_manager_status === 'received')
                                <span class="badge badge-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i> Received - Good Condition
                                </span>
                                @elseif($delivery->site_manager_status === 'issues_noted')
                                <span class="badge badge-warning fs-6">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Issues Noted
                                </span>
                                @elseif($delivery->site_manager_status === 'completed')
                                <span class="badge badge-primary fs-6">
                                    <i class="bi bi-check-circle-fill me-1"></i> Completed
                                </span>
                                @else
                                <span class="badge badge-secondary fs-6">Pending</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Comments</label>
                            <p class="mb-0">{{ $delivery->site_manager_comments }}</p>
                        </div>

                        <div class="mb-0">
                            <label class="small text-muted">Updated By</label>
                            <p class="mb-0">
                                {{ $delivery->siteManagerUpdatedBy->firstname }}
                                {{ $delivery->siteManagerUpdatedBy->lastname }}<br>
                                <small
                                    class="text-muted">{{ $delivery->site_manager_updated_at->format('M d, Y g:i A') }}</small>
                            </p>
                        </div>
                        @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Site manager has not provided feedback on this delivery yet.
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Quality Notes -->
                @if($delivery->quality_notes)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Quality Notes</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $delivery->quality_notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Attachments -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Attachments</h4>
                    </div>
                    <div class="card-body">
                        @if($delivery->attachments && count($delivery->attachments) > 0)
                        <div class="row g-3">
                            @foreach($delivery->attachments as $index => $attachment)
                            <div class="col-md-4">
                                <div class="card border">
                                    @if(str_starts_with($attachment['type'], 'image/'))
                                    <img src="{{ Storage::url($attachment['path']) }}" class="card-img-top"
                                        alt="{{ $attachment['original_name'] }}"
                                        style="max-height: 200px; object-fit: cover;">
                                    @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                        style="height: 200px;">
                                        <i class="bi bi-file-pdf text-danger" style="font-size: 4rem;"></i>
                                    </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <p class="mb-1 small text-truncate" title="{{ $attachment['original_name'] }}">
                                            {{ $attachment['original_name'] }}
                                        </p>
                                        <small class="text-muted">{{ number_format($attachment['size'] / 1024, 2) }}
                                            KB</small>
                                        <div class="mt-2">
                                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank"
                                                class="btn btn-sm btn-info w-100 mb-1">
                                                <i class="bi bi-eye me-1"></i> View
                                            </a>
                                            @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                            <form action="{{ route('deliveries.delete-attachment', $delivery) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this attachment?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="index" value="{{ $index }}">
                                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark display-1 text-muted"></i>
                            <h5 class="mt-3">No Attachments</h5>
                            <p class="text-muted">No photos or documents were uploaded with this delivery.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Request Context -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Request Context</h4>
                        <a href="{{ route('reports.delivery-receipt', ['delivery' => $delivery, 'download' => '1']) }}"
                            class="btn btn-sm btn-info me-2" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> Download Receipt
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Request Number</label>
                                <div>
                                    <a href="{{ route('requests.show', $delivery->request) }}"
                                        class="fw-bold text-primary">
                                        {{ $delivery->request->request_number }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Project</label>
                                <div>{{ $delivery->request->project->name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Ordered Quantity</label>
                                <div>{{ number_format($delivery->requestItem->quantity, 2) }}
                                    {{ $delivery->requestItem->material->unit_of_measurement }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Total Delivered (All Deliveries)</label>
                                @php
                                $totalDelivered = $delivery->requestItem->deliveries()
                                ->whereIn('verification_status', ['accepted', 'partial'])
                                ->sum('quantity_delivered');
                                @endphp
                                <div
                                    class="{{ $totalDelivered >= $delivery->requestItem->quantity ? 'text-success fw-bold' : 'text-warning' }}">
                                    {{ number_format($totalDelivered, 2) }}
                                    {{ $delivery->requestItem->material->unit_of_measurement }}
                                    @if($totalDelivered >= $delivery->requestItem->quantity)
                                    <i class="bi bi-check-circle ms-1"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection