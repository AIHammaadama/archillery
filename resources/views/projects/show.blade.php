@extends('layouts.admin')

@section('title', $project->name . ' | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ $project->name }}</h4>
                    <p class="mb-0">{{ $project->code }}</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item active">{{ $project->code }}</li>
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

        <!-- Analytics Cards -->
        @if(auth()->user()->hasPermission('view-request-pricing'))
        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div
                                class="avatar-sm bg-primary-light rounded me-3 d-flex align-items-center justify-content-center text-primary">
                                <i class="bi bi-wallet2 fs-5"></i>
                            </div>
                            <span class="fs-14 d-block">Total Budget</span>
                        </div>
                        <h4 class="mb-0">₦{{ number_format($project->budget, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div
                                class="avatar-sm bg-success-light rounded me-3 d-flex align-items-center justify-content-center text-success">
                                <i class="bi bi-cash-stack fs-5"></i>
                            </div>
                            <span class="fs-14 d-block">Total Spent</span>
                        </div>
                        <h4 class="mb-0">₦{{ number_format($stats['spent_amount'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div
                                class="avatar-sm bg-warning-light rounded me-3 d-flex align-items-center justify-content-center text-warning">
                                <i class="bi bi-pie-chart fs-5"></i>
                            </div>
                            <span class="fs-14 d-block">Budget Usage</span>
                        </div>
                        <h4 class="mb-0">{{ $stats['budget_percentage'] }}%</h4>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-accent card-accent-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div
                                class="avatar-sm bg-info-light rounded me-3 d-flex align-items-center justify-content-center text-info">
                                <i class="bi bi-file-text fs-5"></i>
                            </div>
                            <span class="fs-14 d-block">Requests</span>
                        </div>
                        <h4 class="mb-0">{{ $stats['total_requests'] }} <small
                                class="text-muted fs-6 fw-normal">({{ $stats['pending_requests'] }} pending)</small>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <!-- Left Column: Budget & Requests -->
            <div class="col-lg-8">
                <!-- Budget Progress -->
                @if(auth()->user()->hasPermission('view-request-pricing'))
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title">Budget Utilization</h4>
                    </div>
                    <div class="card-body pt-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Progress</span>
                            <span class="fw-bold">{{ $stats['budget_percentage'] }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-{{ $stats['budget_percentage'] > 90 ? 'danger' : ($stats['budget_percentage'] > 75 ? 'warning' : 'success') }}"
                                role="progressbar" style="width: {{ $stats['budget_percentage'] }}%"
                                aria-valuenow="{{ $stats['budget_percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-4">
                                <small class="text-muted d-block">Available Funds</small>
                                <h5 class="text-secondary fw-bold">
                                    ₦{{ number_format($project->budget - $stats['spent_amount'], 2) }}</h5>
                            </div>
                            <div class="col-sm-4 border-start border-end text-center">
                                <small class="text-muted d-block">Approved Requests</small>
                                <h5 class="text-primary fw-bold">{{ $stats['approved_requests'] }}</h5>
                            </div>
                            <div class="col-sm-4 text-end">
                                <small class="text-muted d-block">Pending Requests</small>
                                <h5 class="text-warning fw-bold">{{ $stats['pending_requests'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Procurement Requests Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h4 class="card-title mb-0">Procurement Activity</h4>
                        @can('create', App\Models\ProcurementRequest::class)
                        @if($project->isAssignedTo(auth()->user(), 'site_manager'))
                        <a href="{{ route('requests.create', ['project_id' => $project->id]) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Request
                        </a>
                        @endif
                        @endcan
                    </div>
                    <div class="card-body p-0">
                        @if($project->procurementRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-top-0">Request #</th>
                                        <th class="border-top-0">Requested By</th>
                                        <th class="border-top-0">Date</th>
                                        <th class="border-top-0">Status</th>
                                        @if(auth()->user()->hasPermission('view-request-pricing'))
                                        <th class="border-top-0 text-end">Amount</th>
                                        @endif
                                        <th class="border-top-0 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->procurementRequests->take(5) as $req)
                                    <tr>
                                        <td>
                                            <a href="{{ route('requests.show', $req) }}" class="fw-bold text-primary">
                                                {{ $req->request_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span
                                                        class="avatar-title rounded-circle bg-light text-secondary small">
                                                        {{ strtoupper(substr($req->requestedBy->firstname ?? 'U', 0, 1)) }}
                                                    </span>
                                                </div>
                                                {{ optional($req->requestedBy)->firstname ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>{{ $req->request_date->format('M d') }}</td>
                                        <td>
                                            <span
                                                class="badge badge-subtle-{{ $req->status->badgeClass() }} text-{{ $req->status->badgeClass() }}">
                                                {{ $req->status->label() }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->hasPermission('view-request-pricing'))
                                        <td class="text-end fw-bold">
                                            @if($req->total_quoted_amount)
                                            ₦{{ number_format($req->total_quoted_amount, 2) }}
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td class="text-end">
                                            @can('view', $req)
                                            <a href="{{ route('requests.show', $req) }}"
                                                class="btn btn-xs btn-outline-info">
                                                View
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($project->procurementRequests->count() > 5)
                            <div class="text-center p-3 border-top">
                                <a href="{{ route('projects.requests', $project) }}"
                                    class="text-primary text-decoration-none">View All Requests</a>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="mt-2 text-muted">No procurement activity yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Team -->
            <div class="col-lg-4">
                <!-- Project Info -->
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Project Info&nbsp;</h4>
                            @can('update', $project)
                            <a title="Edit" href="{{ route('projects.edit', $project) }}" class="text-muted"><i
                                    class="bi bi-pencil text-primary"></i></a>
                            @endcan
                        </div>
                        @can('view', $project)
                        <a title="Download Summary Report"
                            href="{{ route('reports.project-summary', ['project' => $project, 'download' => '1']) }}"
                            class="btn btn-sm btn-info me-2" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> Download PDF
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Status</label>
                            <div>
                                <span class="badge badge-{{ $project->getStatusBadgeClass() }} px-3 py-2">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Duration</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-range me-2 text-primary"></i>
                                @if($project->start_date && $project->end_date)
                                {{ $project->start_date->format('M d, Y') }} -
                                {{ $project->end_date->format('M d, Y') }}
                                @else
                                <span class="text-muted">Not scheduled</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Location</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2 text-danger"></i>
                                {{ $project->location ?? 'Not specified' }}
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small">Description</label>
                            <p class="small mb-0">{{ Str::limit($project->description ?? 'No description.', 150) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title mb-0">Project Team</h4>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-3">Site Managers</h6>
                        @forelse($project->siteManagers as $sm)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-2">
                                <div class="avatar-title bg-primary-light text-primary rounded-circle">
                                    {{ substr($sm->firstname, 0, 1) }}{{ substr($sm->lastname, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0 small fw-bold">{{ $sm->firstname }} {{ $sm->lastname }}</h6>
                                <small class="text-muted">{{ $sm->email }}</small>
                            </div>
                        </div>
                        @empty
                        <span class="text-muted small d-block mb-3">No active site managers</span>
                        @endforelse

                        <h6 class="text-muted small text-uppercase fw-bold mb-3 mt-4">Procurement Officers</h6>
                        @forelse($project->procurementOfficers as $po)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-2">
                                <div class="avatar-title bg-info-light text-info rounded-circle">
                                    {{ substr($po->firstname, 0, 1) }}{{ substr($po->lastname, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0 small fw-bold">{{ $po->firstname }} {{ $po->lastname }}</h6>
                                <small class="text-muted">{{ $po->phone }}</small>
                            </div>
                        </div>
                        @empty
                        <span class="text-muted small d-block mb-3">No active procurement officers</span>
                        @endforelse
                    </div>
                </div>

                <!-- Attachments -->
                <div class="card">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Files</h4>
                        <span class="badge bg-secondary">{{ count($project->attachments ?? []) }}</span>
                    </div>
                    <div class="card-body">
                        @if($project->attachments && count($project->attachments) > 0)
                        <div class="row g-3">
                            @foreach($project->attachments as $index => $attachment)
                            <div class="col-md-6 mb-3">
                                <div class="card border h-100">
                                    @if(str_starts_with($attachment['type'] ?? '', 'image/'))
                                    <div style="height: 150px; overflow: hidden; background-color: #f8f9fa;"
                                        class="d-flex align-items-center justify-content-center">
                                        <img src="{{ Storage::url($attachment['path']) }}" class="card-img-top"
                                            alt="{{ $attachment['original_name'] }}"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                        style="height: 150px;">
                                        <i class="bi bi-file-pdf text-danger" style="font-size: 3rem;"></i>
                                    </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <p class="mb-1 small text-truncate fw-bold"
                                            title="{{ $attachment['original_name'] }}">
                                            {{ $attachment['original_name'] }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small
                                                class="text-muted">{{ number_format(($attachment['size'] ?? 0) / 1024, 0) }}
                                                KB</small>

                                            <div class="btn-group">
                                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank"
                                                    class="btn btn-xs btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                                <form action="{{ route('projects.delete-attachment', $project) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Delete this attachment?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="index" value="{{ $index }}">
                                                    <button type="submit" class="btn btn-xs btn-outline-danger"
                                                        title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center text-muted small py-3">No files uploaded</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .avatar-xs {
        width: 30px;
        height: 30px;
    }

    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .badge-subtle-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .badge-subtle-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .badge-subtle-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .badge-subtle-info {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }

    .extra-small {
        font-size: 0.75rem;
    }

    .fs-14 {
        font-size: 14px;
    }
</style>
@endsection