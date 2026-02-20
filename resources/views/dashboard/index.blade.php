@extends('layouts.admin')

@section('title', 'Dashboard | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Welcome Header -->
        <div class="form-head d-flex mb-4 align-items-start">
            <div class="mr-auto">
                <h3 class="text-orange font-w600">Welcome to {{ config('app.short_name') }}, <span
                        class="text-orange">{{ Auth::user()->firstname. ' ' .Auth::user()->lastname }}!</span></h3>
                <p class="text-muted mb-0">
                    @if($user->hasRole('site_manager'))
                    Manage your project requests and track procurement status
                    @elseif($user->hasRole('procurement_officer'))
                    Review pending vendor assignments and process requests
                    @elseif($user->hasRole('director'))
                    Review and approve procurement requests
                    @else
                    Monitor system-wide procurement activities
                    @endif
                </p>
            </div>
        </div>

        <!-- Role-Specific Dashboard -->
        @if(isset($dashboardData['stats']))
        <!-- Stats Cards -->
        <div class="row">
            @if($user->hasRole('site_manager'))
            <!-- Site Manager Stats -->
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">My Projects</p>
                                <h3 class="mb-0 text-primary">{{ $dashboardData['stats']['my_projects'] }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="bi bi-building text-primary fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">My Requests</p>
                                <h3 class="mb-0 text-success">{{ $dashboardData['stats']['my_requests'] }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="bi bi-file-earmark-text text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Pending</p>
                                <h3 class="mb-0 text-warning">{{ $dashboardData['stats']['pending_requests'] }}</h3>
                            </div>
                            <div class="icon-box bg-warning-light">
                                <i class="bi bi-hourglass-split text-warning fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Approved</p>
                                <h3 class="mb-0 text-success">{{ $dashboardData['stats']['approved_requests'] }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @elseif($user->hasRole('procurement_officer'))
            <!-- Procurement Officer Stats -->
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Assigned Projects</p>
                                <h3 class="mb-0 text-primary">{{ $dashboardData['stats']['assigned_projects'] }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="bi bi-building text-primary fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Pending Assignments</p>
                                <h3 class="mb-0 text-warning">{{ $dashboardData['stats']['pending_assignments'] }}</h3>
                            </div>
                            <div class="icon-box bg-warning-light">
                                <i class="bi bi-clipboard-check text-warning fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Processed</p>
                                <h3 class="mb-0 text-success">{{ $dashboardData['stats']['processed_requests'] }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Active Vendors</p>
                                <h3 class="mb-0 text-info">{{ $dashboardData['stats']['active_vendors'] }}</h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="bi bi-people text-info fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @elseif($user->hasRole('director'))
            <!-- Director Stats -->
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Total Projects</p>
                                <h3 class="mb-0 text-primary">{{ $dashboardData['stats']['total_projects'] }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="bi bi-building text-primary fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Pending Approvals</p>
                                <h3 class="mb-0 text-warning">{{ $dashboardData['stats']['pending_approvals'] }}</h3>
                            </div>
                            <div class="icon-box bg-warning-light">
                                <i class="bi bi-clock-history text-warning fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Approved This Month</p>
                                <h3 class="mb-0 text-success">{{ $dashboardData['stats']['approved_this_month'] }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Budget Utilization</p>
                                <h3 class="mb-0 text-info">{{ $dashboardData['stats']['budget_utilization'] }}%</h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="bi bi-graph-up text-info fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @else
            <!-- Admin Stats -->
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Total Users</p>
                                <h3 class="mb-0 text-primary">{{ $dashboardData['stats']['total_users'] }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="bi bi-people text-primary fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Total Projects</p>
                                <h3 class="mb-0 text-success">{{ $dashboardData['stats']['total_projects'] }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="bi bi-building text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Total Requests</p>
                                <h3 class="mb-0 text-warning">{{ $dashboardData['stats']['total_requests'] }}</h3>
                            </div>
                            <div class="icon-box bg-warning-light">
                                <i class="bi bi-file-earmark-text text-warning fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card card-accent card-accent-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-2 text-muted">Total Vendors</p>
                                <h3 class="mb-0 text-info">{{ $dashboardData['stats']['total_vendors'] }}</h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="bi bi-shop text-info fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Charts and Activity -->
        <div class="row">
            <!-- Charts Column -->
            <div class="col-lg-8">
                @if(isset($dashboardData['charts']))
                <!-- Request Trends Chart -->
                @if(isset($dashboardData['charts']['request_trends']) ||
                isset($dashboardData['charts']['monthly_trends']))
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">
                            @if($user->hasRole('director') || $user->hasAnyRole(['admin', 'super_admin']))
                            Monthly Procurement Trends
                            @else
                            Request Trends
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="trendsChart" height="100"></canvas>
                    </div>
                </div>
                @endif

                <!-- Status Distribution / Vendor Engagement -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            @if($user->hasRole('procurement_officer') &&
                            isset($dashboardData['charts']['vendor_engagement']))
                            Vendor Engagement
                            @else
                            Request Status Distribution
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <canvas id="distributionChart" height="100"></canvas>
                    </div>
                </div>
                @endif
            </div>

            <!-- Recent Activity / Quick Actions -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        @if($user->hasRole('site_manager'))
                        <a href="{{ route('requests.create') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-plus-circle me-2"></i> New Request
                        </a>
                        <a href="{{ route('requests.index') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-list-ul me-2"></i> My Requests
                        </a>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-building me-2"></i> My Projects
                        </a>
                        @elseif($user->hasRole('procurement_officer'))
                        <a href="{{ route('approvals.procurement-queue') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-clipboard-check me-2"></i> Procurement Queue
                        </a>
                        <a href="{{ route('vendors.index') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-people me-2"></i> Vendors
                        </a>
                        <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-box-seam me-2"></i> Materials
                        </a>
                        @elseif($user->hasRole('director'))
                        <a href="{{ route('approvals.director-queue') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-circle me-2"></i> Approval Queue
                        </a>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-building me-2"></i> All Projects
                        </a>
                        <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-file-earmark-text me-2"></i> All Requests
                        </a>
                        @else
                        <a href="{{ route('projects.index') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-building me-2"></i> Projects
                        </a>
                        <a href="{{ route('users') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-people me-2"></i> Users
                        </a>
                        <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-shop me-2"></i> Vendors
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Activity</h4>
                    </div>
                    <div class="card-body">
                        @if(isset($dashboardData['recent_requests']) && $dashboardData['recent_requests']->count() > 0)
                        <div class="activity-list">
                            @foreach($dashboardData['recent_requests'] as $req)
                            <div class="activity-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <a href="{{ route('requests.show', $req) }}" class="fw-bold text-primary">
                                            {{ $req->request_number }}
                                        </a>

                                        <p class="mb-1 small text-muted">{{ $req->project->name }}</p>
                                        <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge bg-{{ $req->status->badgeClass() }}">
                                        {{ $req->status->label() }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @elseif(isset($dashboardData['recent_activity']) && $dashboardData['recent_activity']->count() >
                        0)
                        <div class="activity-list">
                            @foreach($dashboardData['recent_activity'] as $req)
                            <div class="activity-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <a href="{{ route('requests.show', $req) }}" class="fw-bold text-primary">
                                            {{ $req->request_number }}
                                        </a>
                                        <p class="mb-1 small text-muted">{{ $req->project->name }}</p>
                                        <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge bg-{{ $req->status->badgeClass() }}">
                                        {{ $req->status->label() }}
                                    </span>

                                </div>
                            </div>
                            @endforeach
                        </div>
                        @elseif(isset($dashboardData['approval_queue']) && $dashboardData['approval_queue']->count() >
                        0)
                        <div class="activity-list">
                            @foreach($dashboardData['approval_queue'] as $req)
                            <div class="activity-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <a href="{{ route('requests.show', $req) }}" class="fw-bold text-primary">
                                            {{ $req->request_number }}
                                        </a>
                                        <p class="mb-1 small text-muted">{{ $req->project->name }}</p>
                                        <small class="text-muted">{{ $req->items->count() }} items</small>
                                    </div>
                                    <span class="badge badge-warning">
                                        Pending
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted text-center py-3">No recent activity</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Fallback for users without dashboard data -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-speedometer2 display-1 text-muted"></i>
                        <h4 class="mt-3">Dashboard Not Available</h4>
                        <p class="text-muted">Your role does not have a dashboard configured yet.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>

</style>

@if(isset($dashboardData['charts']))
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trends Chart
    @if(isset($dashboardData['charts']['request_trends']))
    const trendsData = @json($dashboardData['charts']['request_trends']);
    const trendsCtx = document.getElementById('trendsChart');
    if (trendsCtx) {
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: trendsData.labels,
                datasets: [{
                    label: 'Requests',
                    data: trendsData.data,
                    borderColor: 'rgba(124, 58, 237, 0.8)',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    @elseif(isset($dashboardData['charts']['monthly_trends']))
    const monthlyData = @json($dashboardData['charts']['monthly_trends']);
    const trendsCtx = document.getElementById('trendsChart');
    if (trendsCtx) {
        new Chart(trendsCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.labels,
                datasets: [{
                    label: 'Requests',
                    data: monthlyData.requests,
                    backgroundColor: 'rgba(124, 58, 237, 0.8)',
                    borderColor: 'rgb(124, 58, 237)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    @endif

    // Distribution / Vendor Chart
    @if(isset($dashboardData['charts']['vendor_engagement']))
    const vendorData = @json($dashboardData['charts']['vendor_engagement']);
    const distCtx = document.getElementById('distributionChart');
    if (distCtx) {
        new Chart(distCtx, {
            type: 'bar',
            data: {
                labels: vendorData.labels,
                datasets: [{
                    label: 'Transactions',
                    data: vendorData.data,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    @elseif(isset($dashboardData['charts']['status_distribution']))
    const statusData = @json($dashboardData['charts']['status_distribution']);
    const distCtx = document.getElementById('distributionChart');
    if (distCtx) {
        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.labels,
                datasets: [{
                    data: statusData.data,
                    backgroundColor: [
                        'rgba(124, 58, 237, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endif
@endsection