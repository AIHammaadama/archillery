@extends('layouts.admin')

@section("title", "Audit Logs | PPMS")

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Audit Logs</h4>
                    <p class="mb-0">System activity and change history</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Audit Logs</li>
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
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Filters Card -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel me-2"></i>Filters
                </h5>
                <button class="btn btn-sm btn-link" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body">
                    <form method="GET" action="{{ route('audits') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Event Type</label>
                                <select name="event" class="form-select form-select-sm">
                                    <option value="">All Events</option>
                                    @foreach($events as $event)
                                    <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                        {{ ucfirst($event) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-select form-select-sm">
                                    <option value="">All Users</option>
                                    @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->firstname }} {{ $u->lastname }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Model</label>
                                <select name="model_type" class="form-select form-select-sm">
                                    <option value="">All Models</option>
                                    @foreach($modelTypes as $type)
                                    <option value="{{ $type['value'] }}"
                                        {{ request('model_type') == $type['value'] ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="{{ route('audits') }}" class="btn btn-outline-secondary btn-md">
                                    Reset
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" name="q" value="{{ request('q') }}"
                                        placeholder="Search by user, old/new values...">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Audit Records
                    <span class="badge badge-info ms-2">{{ $audits->total() }} total</span>
                </h4>
            </div>
            <div class="card-body">
                @if($audits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Model</th>
                                <th>Timestamp</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($audits as $audit)
                            <tr>
                                <td>{{ $loop->iteration + ($audits->currentPage() - 1) * $audits->perPage() }}</td>
                                <td>
                                    @if($audit->email)
                                    <div>{{ $audit->firstname }} {{ $audit->lastname }}</div>
                                    <small class="text-muted">{{ $audit->email }}</small>
                                    @else
                                    <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    @if($audit->event === 'created')
                                    <span class="badge badge-success">Created</span>
                                    @elseif($audit->event === 'updated')
                                    <span class="badge badge-warning">Updated</span>
                                    @elseif($audit->event === 'deleted')
                                    <span class="badge badge-danger">Deleted</span>
                                    @else
                                    <span class="badge badge-secondary">{{ ucfirst($audit->event) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ class_basename($audit->auditable_type) }}</span>
                                    <small class="text-muted d-block">ID: {{ $audit->auditable_id }}</small>
                                </td>
                                <td>
                                    <div>{{ $audit->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $audit->created_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('view-audit', $audit->id) }}" class="btn btn-sm btn-info"
                                        title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $audits->links('pagination::bootstrap-4') }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="mt-3">No Audit Records Found</h5>
                    <p class="text-muted">Try adjusting your filters or search criteria.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection