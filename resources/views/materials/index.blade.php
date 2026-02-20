@extends('layouts.admin')

@section('title', 'Materials Catalog | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Materials Catalog</h4>
                    <p class="mb-0">Browse construction materials and supplies</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Materials</li>
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

        <!-- Materials catalog -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Materials ({{ $materials->total() }})</h4>
                        @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                        <a href="{{ route('materials.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Material
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('materials.index') }}" class="row mb-4">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search materials by name, code, or description..."
                                        value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-control">
                                    <option value="all">All Categories</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100 form-control btn-primary text-light">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                            </div>
                        </form>

                        <!-- Materials grid -->
                        @if($materials->count() > 0)
                        <div class="row g-3">
                            @foreach($materials as $material)
                            <div class="col-md-6 col-lg-4">
                                <div class="card material-card h-100 card-accent ">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <a href="{{ route('materials.show', $material) }}"
                                                        class="text-secondary">
                                                        {{ $material->name }}
                                                    </a>
                                                </h5>
                                                <p class="small text-muted mb-0">{{ $material->code }}</p>
                                            </div>
                                            @if($material->is_active)
                                            <span class="badge badge-success">Active</span>
                                            @else
                                            <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </div>

                                        @if($material->category)
                                        <span class="badge badge-danger mb-2">{{ $material->category }}</span>
                                        @endif

                                        @if($material->description)
                                        <p class="card-text small text-muted mb-2">
                                            {{ Str::limit($material->description, 80) }}
                                        </p>
                                        @endif

                                        <div
                                            class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                                            <div>
                                                @if($material->unit_of_measurement)
                                                <small class="text-muted">
                                                    <i class="bi bi-box"></i>
                                                    {{ ucfirst($material->unit_of_measurement) }}
                                                </small>
                                                @endif
                                            </div>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('materials.show', $material) }}"
                                                    class="btn btn-sm btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                                <a href="{{ route('materials.edit', $material) }}"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @endif
                                                @if(auth()->user()->hasAnyRole(['admin', 'super_admin',
                                                'director']))
                                                <a href="{{ route('materials.destroy', $material) }}"
                                                    class="btn btn-sm btn-danger" title="Delete"
                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this material?')) { document.getElementById('delete-form-{{ $material->id }}').submit(); }">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $material->id }}"
                                                    action="{{ route('materials.destroy', $material) }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($materials->hasPages())
                        <div class="mt-4">
                            {{ $materials->links('pagination::bootstrap-4') }}
                        </div>
                        @endif
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-box-seam display-1 text-muted"></i>
                            <h5 class="mt-3">No materials found</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'category', 'is_active']))
                                Try adjusting your filters.
                                <a href="{{ route('materials.index') }}">Clear filters</a>
                                @elseif(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                <a href="{{ route('materials.create') }}">Add your first material</a>
                                @else
                                The materials catalog is empty.
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .material-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid var(--border-color);
    }

    .material-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .material-card .card-title a {
        text-decoration: none;
    }

    .material-card .card-title a:hover {
        color: var(--color-primary) !important;
    }
</style>
@endsection