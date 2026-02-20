@extends('layouts.admin')

@section('title', 'Edit Material | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Material</h4>
                    <p class="mb-0">Update material information</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('materials.index') }}">Materials</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('materials.show', $material) }}">{{ $material->code }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Material Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('materials.update', $material) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Material Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $material->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Material Code</label>
                                    <input type="text" class="form-control" value="{{ $material->code }}" readonly
                                        disabled>
                                    <small class="text-muted">Auto-generated, cannot be changed</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control @error('category') is-invalid @enderror"
                                        required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('category', $material->category) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Unit of Measurement <span
                                            class="text-danger">*</span></label>
                                    <select name="unit_of_measurement"
                                        class="form-control @error('unit_of_measurement') is-invalid @enderror"
                                        required>
                                        <option value="">Select Unit</option>
                                        @foreach($units as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('unit_of_measurement', $material->unit_of_measurement) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('unit_of_measurement')<div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        rows="3">{{ old('description', $material->description) }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Specifications <small class="text-muted">(JSON
                                            format)</small></label>
                                    <textarea name="specifications"
                                        class="form-control @error('specifications') is-invalid @enderror" rows="4"
                                        placeholder='{"brand": "Dangote", "grade": "42.5R", "packaging": "50kg bags"}'>{{ old('specifications', is_array($material->specifications) ? json_encode($material->specifications, JSON_PRETTY_PRINT) : $material->specifications) }}</textarea>
                                    <small class="text-muted">Enter material specifications in JSON format. Example:
                                        {"brand": "Dangote", "grade": "42.5R"}</small>
                                    @error('specifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="is_active"
                                            {{ old('is_active', $material->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('materials.show', $material) }}" class="btn btn-light">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Update Material
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection