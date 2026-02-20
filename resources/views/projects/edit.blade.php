@extends('layouts.admin')

@section('title', 'Edit Project | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Edit Project</h4>
                    <p class="mb-0">Update project information</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('projects.show', $project) }}">{{ $project->code }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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

        <!-- Edit form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Project Information</h4>
                    </div>
                    <div class="card-body" x-data="projectForm()">
                        <form action="{{ route('projects.update', $project) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $project->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Project Code</label>
                                    <input type="text" class="form-control" value="{{ $project->code }}" readonly
                                        disabled>
                                    <small class="text-muted">Auto-generated, cannot be changed</small>
                                </div>

                                <!-- Project Photo -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Project Photos</label>
                                    <input type="file" name="attachments[]"
                                        class="form-control @error('attachments.*') is-invalid @enderror" multiple
                                        accept="image/jpeg,image/png,image/jpg,application/pdf">
                                    <small class="text-muted">Max 5MB per file. Accepted: JPEG, PNG, PDF</small>
                                    @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        rows="3">{{ old('description', $project->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Project Location</label>
                                    <input type="text" name="location"
                                        class="form-control @error('location') is-invalid @enderror" rows="3"
                                        value="{{ old('location', $project->location) }}">
                                    @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Location -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">State</label>
                                    <select name="state_id" class="form-control @error('state_id') is-invalid @enderror"
                                        x-model="selectedState" @change="loadLgas()">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', $project->state_id) == $state->id ? 'selected' : '' }}>
                                            {{ $state->state }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">LGA</label>
                                    <select name="lga_id" class="form-control @error('lga_id') is-invalid @enderror"
                                        x-model="selectedLga" :disabled="!selectedState || lgasLoading">
                                        <option value="">Select LGA</option>
                                        <template x-for="lga in lgas" :key="lga.id">
                                            <option :value="lga.id" x-text="lga.lga"
                                                :selected="lga.id == {{ old('lga_id', $project->lga_id ?? 'null') }}">
                                            </option>
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
                                        value="{{ old('budget', $project->budget) }}" step="0.01" min="0">
                                    @error('budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}">
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror"
                                        required>
                                        <option value="planning"
                                            {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>
                                            Planning</option>
                                        <option value="active"
                                            {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="on_hold"
                                            {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On
                                            Hold</option>
                                        <option value="completed"
                                            {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="cancelled"
                                            {{ old('status', $project->status) === 'cancelled' ? 'selected' : '' }}>
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
                                            {{ in_array($manager->id, old('site_managers', $assignedSiteManagers)) ? 'selected' : '' }}>
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
                                            {{ in_array($officer->id, old('procurement_officers', $assignedProcurementOfficers)) ? 'selected' : '' }}>
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
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-light">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Update Project
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
        selectedState: '{{ old("state_id", $project->state_id) }}',
        selectedLga: '{{ old("lga_id", $project->lga_id) }}',
        lgas: @json($lgas),
        lgasLoading: false,

        init() {
            if (this.selectedState && this.lgas.length === 0) {
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