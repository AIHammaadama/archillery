@extends('layouts.admin')

@section('title', 'Vendors | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Vendors</h4>
                    <p class="mb-0">Manage vendor information</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Vendors</li>
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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Vendors</h4>
                        @if(auth()->user()->hasPermission('create-vendors'))
                        <a href="{{ route('vendors.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Vendor
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="vendorsTable" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Location</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vendors as $vendor)
                                    <tr>
                                        <td><a href="{{ route('vendors.show', $vendor) }}"
                                                class="fw-bold text-primary">{{ $vendor->code }}</a></td>
                                        <td>{{ $vendor->name }}</td>
                                        <td>{{ $vendor->phone }}</td>
                                        <td>{{ $vendor->state?->state ?? 'N/A' }}</td>
                                        <td>
                                            @if($vendor->rating)
                                            <div class="d-flex align-items-center">
                                                <span class="me-1">{{ number_format($vendor->rating, 1) }}</span>
                                                @for($i = 1; $i <= 5; $i++) <i
                                                    class="bi bi-star{{ $i <= $vendor->rating ? '-fill text-warning' : '' }}">
                                                    </i>
                                                    @endfor
                                            </div>
                                            @else
                                            <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td><span
                                                class="badge badge-{{ $vendor->status === 'active' ? 'success' : ($vendor->status === 'suspended' ? 'danger' : 'secondary') }}">{{ ucfirst($vendor->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('vendors.show', $vendor) }}"
                                                class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                            @if(auth()->user()->hasAnyRole(['admin', 'super_admin',
                                            'procurement_officer']))
                                            <a href="{{ route('vendors.edit', $vendor) }}"
                                                class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                            @endif
                                            @if(auth()->user()->hasAnyRole(['admin', 'super_admin',
                                            'director']))
                                            <a href="{{ route('vendors.destroy', $vendor) }}"
                                                class="btn btn-sm btn-danger" title="Delete"
                                                onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this vendor?')) { document.getElementById('delete-form-{{ $vendor->id }}').submit(); }">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <form id="delete-form-{{ $vendor->id }}"
                                                action="{{ route('vendors.destroy', $vendor) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-shop display-1 text-muted"></i>
                                            <h5 class="mt-3">No vendors found</h5>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
    $(document).ready(function() {
        $('#vendorsTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [
                [1, 'asc']
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"B>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-clipboard"></i> Copy'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-file-earmark-csv"></i> CSV'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    orientation: 'landscape'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-printer"></i> Print'
                }
            ],
            initComplete: function() {
                const searchInput = $('#vendorsTable_filter input');

                searchInput
                    .addClass('form-control form-control-sm')
                    .attr('placeholder', 'Search requests..')
                    .css({
                        'width': '180px', // 👈 makes it wide
                        'height': '32px', // 👈 slim height
                        'padding': '0.25rem 0.5rem',
                        'font-size': '0.875rem'
                    });

            },
            columnDefs: [{
                    orderable: false,
                    targets: [6, -1]
                } // Disable sorting on rating and actions columns
            ],
            language: {
                searchPlaceholder: "Search vendors...",
                lengthMenu: "Show _MENU_ vendors",
                info: "Showing _START_ to _END_ of _TOTAL_ vendors",
                zeroRecords: "No matching vendors found"
            }
        });
    });
</script>
@endpush