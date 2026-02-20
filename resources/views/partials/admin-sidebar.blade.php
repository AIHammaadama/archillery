<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu font-sans" id="menu">
            {{-- Dashboard (Available to all authenticated users) --}}
            <li>
                <a class="ai-icon" href="{{ route('dashboard') }}" aria-expanded="false">
                    <i class="flaticon-381-networking fs-26"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Projects (Site Managers see assigned, others see all) --}}
            @if(auth()->user()->hasPermission('view-projects'))
            <li>
                <a class="ai-icon" href="{{ route('projects.index') }}" aria-expanded="false">
                    <i class="mdi mdi-home-modern fs-26"></i>
                    <span class="nav-text">Projects</span>
                </a>
            </li>
            @endif

            {{-- Requests (All roles with request access) --}}
            @if(auth()->user()->hasPermission('view-requests'))
            <li>
                <a class="ai-icon" href="{{ route('requests.index') }}" aria-expanded="false">
                    <i class="mdi mdi-progress-clock fs-26"></i>
                    <span class="nav-text">My Requests</span>
                </a>
            </li>
            @endif

            {{-- Procurement Workflow Section --}}
            @if(auth()->user()->hasPermission('process-purchase-request') ||
            auth()->user()->hasPermission('approve-purchase-request'))
            <hr>
            <li class="nav-label first">Procurement Queues</li>
            @endif

            {{-- Procurement Queue (Procurement Officers) --}}
            @if(auth()->user()->hasPermission('process-purchase-request'))
            <li>
                <a class="ai-icon" href="{{ route('approvals.procurement-queue') }}" aria-expanded="false">
                    <i class="mdi mdi-clock-alert-outline fs-20"></i>
                    <span class="nav-text">Procurement</span>
                    @php
                    $pendingCount = \App\Models\ProcurementRequest::whereIn('status', ['submitted',
                    'pending_procurement', 'procurement_processing'])
                    ->whereHas('project.assignments', function($q) {
                    $q->where('user_id', auth()->id())
                    ->where('role_type', 'procurement_officer')
                    ->where('is_active', true);
                    })->count();
                    @endphp
                    @if($pendingCount > 0)
                    <span class="badge badge-rounded badge-warning">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            @endif

            {{-- Director Approval Queue (Directors) --}}
            @if(auth()->user()->hasPermission('approve-purchase-request'))
            <li>
                <a class="ai-icon" href="{{ route('approvals.director-queue') }}" aria-expanded="false">
                    <i class="mdi mdi-check-circle-outline fs-26"></i>
                    <span class="nav-text">Approvals</span>
                    @php
                    $approvalCount = \App\Models\ProcurementRequest::where('status', 'pending_director')->count();
                    @endphp
                    @if($approvalCount > 0)
                    <span class="badge badge-rounded badge-info">{{ $approvalCount }}</span>
                    @endif
                </a>
            </li>
            @endif

            {{-- Deliveries (Procurement Officers, Directors, Admins) --}}
            @if(auth()->user()->hasPermission('view-deliveries'))
            <li>
                <a class="ai-icon" href="{{ route('deliveries.all') }}" aria-expanded="false">
                    <i class="mdi mdi-truck-delivery fs-26"></i>
                    <span class="nav-text">Deliveries</span>
                    @php
                    $deliveryCount = \App\Models\ProcurementRequest::whereIn('status', ['approved',
                    'partially_delivered'])->count();
                    @endphp
                    @if($deliveryCount > 0)
                    <span class="badge badge-rounded badge-success">{{ $deliveryCount }}</span>
                    @endif
                </a>
            </li>
            @endif

            {{-- Resource Management Section --}}
            @if(auth()->user()->hasPermission('view-vendors') || auth()->user()->hasPermission('view-materials'))
            <hr>
            <li class="nav-label first">Resources</li>
            @endif

            {{-- Vendors (Procurement Officers & above) --}}
            @if(auth()->user()->hasPermission('view-vendors'))
            <li>
                <a class="ai-icon" href="{{ route('vendors.index') }}" aria-expanded="false">
                    <i class="fa fa-users fs-26"></i>
                    <span class="nav-text">Vendors</span>
                </a>
            </li>
            @endif

            {{-- Materials Catalog (All roles) --}}
            @if(auth()->user()->hasPermission('view-materials'))
            <li>
                <a class="ai-icon" href="{{ route('materials.index') }}" aria-expanded="false">
                    <i class="mdi mdi-package-variant fs-26"></i>
                    <span class="nav-text">Materials</span>
                </a>
            </li>
            @endif

            {{-- System Administration Section --}}
            @if(auth()->user()->hasPermission('manage-users') || auth()->user()->hasPermission('view-audits'))
            <hr>
            <li class="nav-label first">Administration</li>
            @endif

            {{-- User Management (Admins only) --}}
            @if(auth()->user()->hasPermission('manage-users'))
            <li>
                <a class="ai-icon" href="{{ route('users') }}" aria-expanded="false">
                    <i class="fa fa-gear fs-26"></i>
                    <span class="nav-text">User Settings</span>
                </a>
            </li>
            @endif

            {{-- Audit Logs (Admins & Super Admins) --}}
            @if(auth()->user()->hasPermission('view-audits'))
            <li>
                <a class="ai-icon" href="{{ route('audits') }}" aria-expanded="false">
                    <i class="mdi mdi-watch fs-26"></i>
                    <span class="nav-text">Audit Logs</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>