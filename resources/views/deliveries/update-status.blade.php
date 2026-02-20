@extends('layouts.admin')

@section('title', 'Update Delivery Status | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Update Delivery Status</h4>
                    <p class="mb-0">{{ $delivery->delivery_number }}</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('requests.show', $delivery->request) }}">{{ $delivery->request->request_number }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('deliveries.show', $delivery) }}">{{ $delivery->delivery_number }}</a></li>
                    <li class="breadcrumb-item active">Update Status</li>
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

        <div class="row">
            <!-- Delivery Details (Read-only) -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Delivery Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Material</label>
                            <div class="fw-bold">{{ $delivery->requestItem->material->name }}</div>
                            <small class="text-muted">{{ $delivery->requestItem->material->code }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Vendor</label>
                            <p class="mb-0">{{ $delivery->vendor ? $delivery->vendor->name : 'N/A' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Delivery Date</label>
                            <p class="mb-0">{{ $delivery->delivery_date->format('M d, Y') }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Quantity Delivered</label>
                            <p class="mb-0 fw-bold text-primary fs-5">
                                {{ number_format($delivery->quantity_delivered, 2) }} {{ $delivery->requestItem->material->unit_of_measurement }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Ordered Quantity</label>
                            <p class="mb-0">{{ number_format($delivery->requestItem->quantity, 2) }} {{ $delivery->requestItem->material->unit_of_measurement }}</p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Verification Status</label>
                            <p class="mb-0">
                                @if($delivery->verification_status === 'accepted')
                                    <span class="badge badge-success"><i class="bi bi-check-circle me-1"></i> Accepted</span>
                                @elseif($delivery->verification_status === 'partial')
                                    <span class="badge badge-warning"><i class="bi bi-exclamation-triangle me-1"></i> Partial</span>
                                @elseif($delivery->verification_status === 'rejected')
                                    <span class="badge badge-danger"><i class="bi bi-x-circle me-1"></i> Rejected</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </p>
                        </div>

                        @if($delivery->quality_notes)
                            <div class="mb-3">
                                <label class="small text-muted">Quality Notes</label>
                                <p class="mb-0 text-muted">{{ $delivery->quality_notes }}</p>
                            </div>
                        @endif

                        <hr>

                        <div class="mb-0">
                            <label class="small text-muted">Verified By</label>
                            <p class="mb-0">
                                @if($delivery->verifiedBy)
                                    {{ $delivery->verifiedBy->firstname }} {{ $delivery->verifiedBy->lastname }}
                                @else
                                    <span class="text-muted">Not verified yet</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Attachments -->
                @if($delivery->attachments && count($delivery->attachments) > 0)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Attachments</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach($delivery->attachments as $attachment)
                                    <div class="col-md-6">
                                        <div class="card border">
                                            @if(str_starts_with($attachment['type'], 'image/'))
                                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank">
                                                    <img src="{{ Storage::url($attachment['path']) }}"
                                                         class="card-img-top"
                                                         alt="{{ $attachment['original_name'] }}"
                                                         style="max-height: 150px; object-fit: cover;">
                                                </a>
                                            @else
                                                <a href="{{ Storage::url($attachment['path']) }}"
                                                   target="_blank"
                                                   class="card-img-top bg-light d-flex align-items-center justify-content-center text-decoration-none"
                                                   style="height: 150px;">
                                                    <i class="bi bi-file-pdf text-danger" style="font-size: 3rem;"></i>
                                                </a>
                                            @endif
                                            <div class="card-body p-2">
                                                <p class="mb-0 small text-truncate" title="{{ $attachment['original_name'] }}">
                                                    {{ $attachment['original_name'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Status Update Form -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Site Manager Feedback</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Important</h6>
                            <p class="mb-0">Please update the delivery status and provide detailed comments about the condition of the materials received. Your feedback will be visible to procurement officers and directors.</p>
                        </div>

                        @if($delivery->siteManagerHasUpdated())
                            <div class="alert alert-warning mb-4">
                                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Previous Update</h6>
                                <p class="mb-2"><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $delivery->site_manager_status)) }}</p>
                                <p class="mb-2"><strong>Comments:</strong> {{ $delivery->site_manager_comments }}</p>
                                <p class="mb-0">
                                    <small class="text-muted">
                                        Updated by {{ $delivery->siteManagerUpdatedBy->firstname }} {{ $delivery->siteManagerUpdatedBy->lastname }}
                                        on {{ $delivery->site_manager_updated_at->format('M d, Y g:i A') }}
                                    </small>
                                </p>
                            </div>
                        @endif

                        <form action="{{ route('deliveries.update-status.store', $delivery) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label">Delivery Status <span class="text-danger">*</span></label>

                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="site_manager_status"
                                           id="status_received"
                                           value="received"
                                           {{ old('site_manager_status', $delivery->site_manager_status) === 'received' ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label w-100" for="status_received">
                                        <div class="fw-bold text-success">
                                            <i class="bi bi-check-circle me-1"></i> Received - Good Condition
                                        </div>
                                        <small class="text-muted">Materials delivered are in good condition with no issues.</small>
                                    </label>
                                </div>

                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="site_manager_status"
                                           id="status_issues"
                                           value="issues_noted"
                                           {{ old('site_manager_status', $delivery->site_manager_status) === 'issues_noted' ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label w-100" for="status_issues">
                                        <div class="fw-bold text-warning">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Issues Noted
                                        </div>
                                        <small class="text-muted">Materials have minor issues or discrepancies that need to be documented.</small>
                                    </label>
                                </div>

                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="site_manager_status"
                                           id="status_completed"
                                           value="completed"
                                           {{ old('site_manager_status', $delivery->site_manager_status) === 'completed' ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label w-100" for="status_completed">
                                        <div class="fw-bold text-primary">
                                            <i class="bi bi-check-circle-fill me-1"></i> Completed
                                        </div>
                                        <small class="text-muted">Materials received, inspected, and all formalities completed.</small>
                                    </label>
                                </div>

                                @error('site_manager_status')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Comments <span class="text-danger">*</span></label>
                                <textarea name="site_manager_comments"
                                          class="form-control @error('site_manager_comments') is-invalid @enderror"
                                          rows="6"
                                          placeholder="Provide detailed comments about:&#10;- Condition of materials received&#10;- Any damages or discrepancies&#10;- Issues that need attention&#10;- Quality concerns&#10;- Other relevant observations"
                                          required>{{ old('site_manager_comments', $delivery->site_manager_comments) }}</textarea>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Your comments will be visible to procurement officers, directors, and other stakeholders. Please be specific and detailed.
                                </small>
                                @error('site_manager_comments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Action buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-light">
                                    <i class="bi bi-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Submit delivery status update? This will notify stakeholders.')">
                                    <i class="bi bi-check-circle me-1"></i> Submit Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
