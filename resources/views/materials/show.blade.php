@extends('layouts.admin')

@section('title', $material->name . ' | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ $material->name }}</h4>
                    <p class="mb-0">{{ $material->code }}</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('materials.index') }}">Materials</a></li>
                    <li class="breadcrumb-item active">{{ $material->code }}</li>
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

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Material Details</h4>
                        @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                        <a href="{{ route('materials.edit', $material) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                @if($material->is_active)
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>

                        @if($material->category)
                        <div class="mb-3">
                            <label class="small text-muted">Category</label>
                            <div><span class="badge badge-primary">{{ $material->category }}</span></div>
                        </div>
                        @endif

                        @if($material->unit_of_measurement)
                        <div class="mb-3">
                            <label class="small text-muted">Unit of Measurement</label>
                            <p class="mb-0">{{ ucfirst($material->unit_of_measurement) }}</p>
                        </div>
                        @endif

                        @if($material->description)
                        <div class="mb-3">
                            <label class="small text-muted">Description</label>
                            <p class="mb-0">{{ $material->description }}</p>
                        </div>
                        @endif

                        @if($material->specifications)
                        <div class="mb-0">
                            <label class="small text-muted">Specifications</label>
                            <div class="mt-2">
                                @foreach($material->specifications as $key => $value)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="fw-bold">{{ $value }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Vendor Pricing</h4>
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->can('manage-vendors'))
                        @if($material->vendors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Price ({{ $material->vendors->first()->pivot->currency ?? 'NGN' }})</th>
                                        <th>Min Order Qty</th>
                                        <th>Lead Time</th>
                                        <th>Valid Until</th>
                                        <th>Preferred</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($material->vendors as $vendor)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $vendor->name }}</div>
                                            <small class="text-muted">{{ $vendor->code }}</small>
                                        </td>
                                        <td class="fw-bold text-success">
                                            ₦{{ number_format($vendor->pivot->price, 2) }}
                                        </td>
                                        <td>{{ $vendor->pivot->minimum_order_quantity }}
                                            {{ $material->unit_of_measurement }}</td>
                                        <td>{{ $vendor->pivot->lead_time_days }} days</td>
                                        <td>
                                            @if($vendor->pivot->valid_until)
                                            {{ \Carbon\Carbon::parse($vendor->pivot->valid_until)->format('M d, Y') }}
                                            @else
                                            <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($vendor->pivot->is_preferred)
                                            <i class="bi bi-star-fill text-warning"></i>
                                            @else
                                            <i class="bi bi-star text-muted"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-shop display-1 text-muted"></i>
                            <h5 class="mt-3">No Vendor Pricing Available</h5>
                            <p class="text-muted">No vendors have been assigned pricing for this material yet.</p>
                        </div>
                        @endif
                        @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Vendor pricing information is not available for your role.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection