@extends('layouts.admin')

@section('title', 'Project Requests | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Project Requests</h4>
                    <p class="mb-0">{{ $project->name }} ({{ $project->code }})</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">Details</a></li>
                    <li class="breadcrumb-item active">Requests</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Procurement Requests</h4>

                        <div class="d-flex gap-2">
                            <!-- Filter Form -->
                            <form action="{{ route('projects.requests', $project) }}" method="GET" class="d-flex gap-2">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    @foreach(App\Enums\RequestStatus::cases() as $status)
                                    <option value="{{ $status->value }}"
                                        {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                    @endforeach
                                </select>

                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search..."
                                        value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                                </div>

                                @if(request('status') || request('search'))
                                <a href="{{ route('projects.requests', $project) }}"
                                    class="btn btn-sm btn-outline-secondary" title="Clear Filters">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                                @endif
                            </form>

                            @if($project->isAssignedTo(auth()->user(), 'site_manager'))
                            @can('create', App\Models\ProcurementRequest::class)
                            <a href="{{ route('requests.create', ['project_id' => $project->id]) }}"
                                class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> New Request
                            </a>
                            @endcan
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Date</th>
                                        <th>Requested By</th>
                                        <th>Description</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $request)
                                    <tr>
                                        <td>
                                            <a href="{{ route('requests.show', $request) }}"
                                                class="fw-bold text-primary">
                                                {{ $request->request_number }}
                                            </a>
                                        </td>
                                        <td>{{ $request->request_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($request->requestedBy)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span
                                                        class="avatar-title rounded-circle bg-light text-secondary small">
                                                        {{ strtoupper(substr($request->requestedBy->firstname, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <span>{{ $request->requestedBy->firstname }}
                                                    {{ $request->requestedBy->lastname }}</span>
                                            </div>
                                            @else
                                            <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($request->description ?? 'No description', 50) }}</td>
                                        <td>
                                            @if(auth()->user()->hasPermission('view-request-pricing'))
                                            <span
                                                class="fw-bold">₦{{ number_format($request->total_estimated_amount, 2) }}</span>
                                            @else
                                            <span class="text-muted fst-italic">Hidden</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-subtle-{{ $request->status->badgeClass() }} text-{{ $request->status->badgeClass() }}">
                                                {{ $request->status->label() }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('requests.show', $request) }}"
                                                    class="btn btn-xs btn-primary" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                <p>No requests found for this project.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $requests->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection