@extends('layouts.admin')

@section('title', 'Procurement Requests | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Procurement Requests</h4>
                    <p class="mb-0">Manage material procurement requests</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Requests</li>
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
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Procurement Requests</h4>

                        <div class="d-flex gap-2">
                            <!-- Filter Form -->
                            <form action="{{ route('requests.index') }}" method="GET" class="d-flex gap-2">
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
                                <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline-secondary"
                                    title="Clear Filters">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                                @endif
                            </form>

                            @can('create', App\Models\ProcurementRequest::class)
                            <a href="{{ route('requests.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> New Request
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="d-flex gap-2 mb-3">
                                {{-- Client actions --}}
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="copyTable('#requestsTable')">
                                    Copy
                                </button>

                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="printTable('#requestsTable', 'Procurement Requests')">
                                    Print
                                </button>
                            </div>
                            @php
                            $showRequestedBy = $requests->contains(function ($req) {
                            return $req->requested_by !== auth()->id();
                            });
                            @endphp
                            <table id="requestsTable" class="table table-hover table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Project</th>
                                        <th>Requested By</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        @if(auth()->user()->hasPermission('view-request-pricing'))
                                        <th>Amount</th>
                                        @endif
                                        <th>Items</th>
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
                                        <td>
                                            <div class="fw-bold">{{ $request->project->name }}</div>
                                            <small class="text-muted">{{ $request->project->code }}</small>
                                        </td>
                                        @if($showRequestedBy)
                                        <td>
                                            @if($request->requested_by === auth()->id())
                                            <span class="text-muted">You</span>
                                            @else
                                            {{ optional($request->requestedBy)->firstname ?? 'N/A' }}
                                            {{ optional($request->requestedBy)->lastname }}
                                            @endif
                                        </td>
                                        @endif
                                        <td>{{ $request->request_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $request->status->badgeClass() }}">
                                                {{ $request->status->label() }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->hasPermission('view-request-pricing'))
                                        <td>
                                            @if($request->total_quoted_amount)
                                            <span
                                                class="fw-bold text-success">₦{{ number_format($request->total_quoted_amount, 2) }}</span>
                                            @else
                                            <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td>{{ $request->items->count() }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @can('view', $request)
                                                <a href="{{ route('requests.show', $request) }}"
                                                    class="btn btn-sm btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @endcan

                                                @if($request->isEditable())
                                                @can('update', $request)
                                                <a href="{{ route('requests.edit', $request) }}"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @endcan

                                                @can('delete', $request)
                                                <form action="{{ route('requests.destroy', $request) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                                @endif
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

@push('scripts')
<script>
    function tableToTSV(tableSelector) {
        const table = document.querySelector(tableSelector);
        if (!table) return '';

        const rows = Array.from(table.querySelectorAll('tr'));
        return rows.map(row => {
            const cells = Array.from(row.querySelectorAll('th,td'));
            return cells.map(cell => {
                // normalize whitespace, strip newlines/tabs to keep TSV clean
                return (cell.innerText || '')
                    .replace(/\s+/g, ' ')
                    .replace(/\t/g, ' ')
                    .trim();
            }).join('\t');
        }).join('\n');
    }

    async function copyTable(tableSelector) {
        const tsv = tableToTSV(tableSelector);

        try {
            await navigator.clipboard.writeText(tsv);
            alert('Table copied to clipboard.');
        } catch (e) {
            // fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = tsv;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('Table copied to clipboard.');
        }
    }

    function printTable(tableSelector, title = 'Print') {
        const table = document.querySelector(tableSelector);
        if (!table) return;

        const printWindow = window.open('', '_blank');
        const styles = `
      <style>
        body { font-family: Arial, sans-serif; padding: 16px; }
        h2 { margin: 0 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
        th { background: #f5f5f5; text-align: left; }
        @media print { button { display: none; } }
      </style>
    `;

        printWindow.document.write(`
      <html>
        <head>
          <title>${title}</title>
          ${styles}
        </head>
        <body>
          <h2>${title}</h2>
          ${table.outerHTML}
          <script>
            window.onload = function () { window.print(); window.close(); };
          <\/script>
        </body>
      </html>
    `);

        printWindow.document.close();
    }
</script>
@endpush