@extends('layouts.admin')

@section('title', 'Create Project | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Create New Project</h4>
                    <p class="mb-0">Add a new construction project</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

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

        <!-- Create form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Project Information</h4>
                    </div>
                    <div class="card-body" x-data="projectForm()">
                        <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Project Photo -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Project Photo</label>
                                    <input type="file" name="attachments[]"
                                        class="form-control @error('attachments.*') is-invalid @enderror" multiple
                                        accept="image/jpeg,image/png,image/jpg,application/pdf">
                                    <small class="text-muted">Max 5MB per file. Accepted: JPEG, PNG, PDF</small>
                                    @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Project Location</label>
                                    <textarea name="location"
                                        class="form-control @error('location') is-invalid @enderror"
                                        rows="3">{{ old('location') }}</textarea>
                                    @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Location -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State</label>
                                    <select name="state_id" class="form-control @error('state_id') is-invalid @enderror"
                                        x-model="selectedState" @change="loadLgas()">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                            {{ $state->state }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">LGA</label>
                                    <select name="lga_id" class="form-control @error('lga_id') is-invalid @enderror"
                                        x-model="selectedLga" :disabled="!selectedState || lgasLoading">
                                        <option value="">Select LGA</option>
                                        <template x-for="lga in lgas" :key="lga.id">
                                            <option :value="lga.id" x-text="lga.lga"></option>
                                        </template>
                                    </select>
                                    <div x-show="lgasLoading" class="small text-muted mt-1">
                                        <i class="bi bi-hourglass-split"></i> Loading LGAs...
                                    </div>
                                    @error('lga_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Budget & Dates -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Budget (₦)</label>
                                    <input type="number" name="budget"
                                        class="form-control @error('budget') is-invalid @enderror"
                                        value="{{ old('budget') }}" step="0.01" min="0" placeholder="0.00">
                                    @error('budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date') }}">
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date') }}">
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror"
                                        required>
                                        <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>
                                            Planning</option>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>On
                                            Hold</option>
                                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Team Assignments -->
                                <div class="col-md-12">
                                    <hr class="my-4">
                                    <h5 class="mb-3">Team Assignments</h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Site Manager(s)</label>
                                    <select name="site_managers[]"
                                        class="form-select select2 @error('site_managers') is-invalid @enderror"
                                        multiple>
                                        @foreach($siteManagers as $manager)
                                        <option value="{{ $manager->id }}"
                                            {{ in_array($manager->id, old('site_managers', [])) ? 'selected' : '' }}>
                                            {{ $manager->firstname }} {{ $manager->lastname }} ({{ $manager->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select
                                        multiple</small>
                                    @error('site_managers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Procurement Officer(s)</label>
                                    <select name="procurement_officers[]"
                                        class="form-select select2 @error('procurement_officers') is-invalid @enderror"
                                        multiple>
                                        @foreach($procurementOfficers as $officer)
                                        <option value="{{ $officer->id }}"
                                            {{ in_array($officer->id, old('procurement_officers', [])) ? 'selected' : '' }}>
                                            {{ $officer->firstname }} {{ $officer->lastname }} ({{ $officer->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select
                                        multiple</small>
                                    @error('procurement_officers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action buttons -->
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('projects.index') }}" class="btn btn-light">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Create Project
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function projectForm() {
        return {
            selectedState: '{{ old("state_id") }}',
            selectedLga: '{{ old("lga_id") }}',
            lgas: [],
            lgasLoading: false,

            init() {
                if (this.selectedState) {
                    this.loadLgas();
                }
            },

            async loadLgas() {
                if (!this.selectedState) {
                    this.lgas = [];
                    this.selectedLga = '';
                    return;
                }

                this.lgasLoading = true;
                try {
                    const response = await fetch(`/api/states/${this.selectedState}/lgas`);
                    if (response.ok) {
                        this.lgas = await response.json();
                    }
                } catch (error) {
                    console.error('Failed to load LGAs:', error);
                } finally {
                    this.lgasLoading = false;
                }
            }
        }
    }
</script>
@endsection