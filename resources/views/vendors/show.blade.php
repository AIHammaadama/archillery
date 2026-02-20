@extends('layouts.admin')

@section('title', $vendor->name . ' | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ $vendor->name }}</h4>
                    <p class="mb-0">Vendor Details</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendors</a></li>
                    <li class="breadcrumb-item active">{{ $vendor->name }}</li>
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

        <div class="row">
            <!-- Vendor Details -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="card-title mb-0">Vendor Information</h4>
                            </div>
                        </div>
                        @if(auth()->user()->can('manage-vendors'))
                        <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Status</label>
                            <div>
                                @if($vendor->status === 'active')
                                <span class="badge badge-success fs-6">Active</span>
                                @elseif($vendor->status === 'suspended')
                                <span class="badge badge-warning fs-6">Suspended</span>
                                @else
                                <span class="badge badge-secondary fs-6">Inactive</span>
                                @endif
                            </div>
                        </div>

                        @if($vendor->rating)
                        <div class="mb-3">
                            <label class="small text-muted">Rating</label>
                            <div class="d-flex align-items-center">
                                <span class="me-2 fw-bold fs-5">{{ number_format($vendor->rating, 1) }}</span>
                                @for($i = 1; $i <= 5; $i++) <i
                                    class="bi bi-star{{ $i <= $vendor->rating ? '-fill text-warning' : '' }} me-1"></i>
                                    @endfor
                            </div>
                        </div>
                        @endif

                        @if($vendor->business_registration)
                        <div class="mb-3">
                            <label class="small text-muted">Registration Number</label>
                            <p class="mb-0">{{ $vendor->business_registration }}</p>
                        </div>
                        @endif

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Contact Person</label>
                            <p class="mb-0 fw-bold">{{ $vendor->contact_person }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Email</label>
                            <p class="mb-0">
                                <a href="mailto:{{ $vendor->email }}" class="text-primary">
                                    <i class="bi bi-envelope me-1"></i>{{ $vendor->email }}
                                </a>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Phone</label>
                            <p class="mb-0">
                                <a href="tel:{{ $vendor->phone }}" class="text-primary">
                                    <i class="bi bi-telephone me-1"></i>{{ $vendor->phone }}
                                </a>
                            </p>
                        </div>

                        @if($vendor->alt_phone)
                        <div class="mb-3">
                            <label class="small text-muted">Alternative Phone</label>
                            <p class="mb-0">
                                <a href="tel:{{ $vendor->alt_phone }}" class="text-primary">
                                    <i class="bi bi-telephone me-1"></i>{{ $vendor->alt_phone }}
                                </a>
                            </p>
                        </div>
                        @endif

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted">Address</label>
                            <p class="mb-0">{{ $vendor->address }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Location</label>
                            <p class="mb-0">
                                @if($vendor->state)
                                {{ $vendor->state->state }}
                                @if($vendor->lga), {{ $vendor->lga->lga }}@endif
                                @else
                                <span class="text-muted">Not specified</span>
                                @endif
                            </p>
                        </div>

                        @if($vendor->bank_name || $vendor->bank_account)
                        <hr>
                        <p class="mb-3 text-muted fs-14">Bank Details</p>

                        @if($vendor->bank_name)
                        <div class="mb-2">
                            <label class="small text-muted">Bank Name</label>
                            <p class="mb-0">{{ $vendor->bank_name }}</p>
                        </div>
                        @endif

                        @if($vendor->bank_account)
                        <div class="mb-0">
                            <label class="small text-muted">Account Number</label>
                            <p class="mb-0">{{ $vendor->bank_account }}</p>
                        </div>
                        @endif

                        @if($vendor->bank_account_name)
                        <div class="mb-0">
                            <label class="small text-muted">Account Name</label>
                            <p class="mb-0">{{ $vendor->bank_account_name }}</p>
                        </div>
                        @endif

                        @if($vendor->tax_id)
                        <div class="mb-0">
                            <label class="small text-muted">Tax ID</label>
                            <p class="mb-0">{{ $vendor->tax_id }}</p>
                        </div>
                        @endif
                        @endif

                        @if($vendor->notes)
                        <hr>
                        <div class="mb-0">
                            <label class="small text-muted">Notes</label>
                            <p class="mb-0">{{ $vendor->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Materials & Transactions -->
            <div class="col-lg-8">
                <!-- Assigned Materials -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Assigned Materials</h4>
                        @if(auth()->user()->can('manage-vendors'))
                        <a href="{{ route('vendors.materials.assign', $vendor) }}" class="btn btn-sm btn-success me-1">
                            <i class="bi bi-box-seam"></i> Assign Materials
                        </a>
                        @endif
                        @if(auth()->user()->hasAnyRole(['admin', 'super_admin', 'director', 'procurement_officer']))
                        <a href="{{ route('reports.vendor-transactions', ['vendor' => $vendor, 'download' => '1']) }}"
                            class="btn btn-sm btn-info me-2" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> Download Report
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($vendor->materials->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Unit Price</th>
                                    <th>Min. Order Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendor->materials as $material)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $material->name }}</div>
                                        <small class="text-muted">{{ $material->code }}</small>
                                    </td>
                                    <td class="fw-bold text-success">
                                        ₦{{ number_format($material->pivot->price, 2) }}
                                        <small class="text-muted">/ {{ $material->unit_of_measurement }}</small>
                                    </td>
                                    <td>
                                        @if($material->pivot->minimum_order_quantity)
                                        {{ number_format($material->pivot->minimum_order_quantity, 2) }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted"></i>
                        <h5 class="mt-3">No Materials Assigned</h5>
                        <p class="text-muted">This vendor hasn't been assigned to any materials yet.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Transactions</h4>
                </div>
                <div class="card-body">
                    @php
                    $recentItems = \App\Models\RequestItem::with(['procurementRequest', 'material'])
                    ->where('vendor_id', $vendor->id)
                    ->whereHas('procurementRequest', function($q) {
                    $q->whereNotIn('status', ['draft', 'rejected']);
                    })
                    ->latest()
                    ->limit(10)
                    ->get();
                    @endphp

                    @if($recentItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Material</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentItems as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('requests.show', $item->procurementRequest) }}"
                                            class="fw-bold text-primary">
                                            {{ $item->procurementRequest->request_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>{{ $item->material->name }}</div>
                                        <small class="text-muted">{{ $item->material->code }}</small>
                                    </td>
                                    <td>{{ number_format($item->quantity, 2) }}
                                        {{ $item->material->unit_of_measurement }}
                                    </td>
                                    <td>₦{{ number_format($item->quoted_unit_price, 2) }}</td>
                                    <td class="fw-bold text-success">₦{{ number_format($item->quoted_total, 2) }}
                                    </td>
                                    <td>{{ $item->procurementRequest->request_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->procurementRequest->status->badgeClass() }}">
                                            {{ $item->procurementRequest->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Transaction Summary -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <label class="small text-muted d-block">Total Transactions</label>
                                <span class="fs-4 fw-bold">{{ $recentItems->count() }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">Total Value</label>
                                <span
                                    class="fs-4 fw-bold text-success">₦{{ number_format($recentItems->sum('quoted_total'), 2) }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">Approved Orders</label>
                                <span class="fs-4 fw-bold text-primary">
                                    {{ $recentItems->filter(fn($i) => $i->procurementRequest->status->value === 'approved')->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-receipt display-1 text-muted"></i>
                        <h5 class="mt-3">No Transactions Yet</h5>
                        <p class="text-muted">This vendor hasn't been used in any procurement requests.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection