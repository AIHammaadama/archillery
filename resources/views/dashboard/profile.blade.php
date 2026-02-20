@extends('layouts.admin')

@section("title")
Profile
@endsection

{{-- Content --}}
@section('content')
@php
$user = Auth::user();
@endphp
<div class="content-body">
    <div class="container-fluid">
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('profile') }}">Profile</a></li>
                <li class="breadcrumb-item active">Edit your information</li>
            </ol>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(!empty($success))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fa fa-check-circle me-2"></i>
            {{ $success }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($msg = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fa fa-check-circle me-2"></i>
            {{ $msg }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @elseif($msg = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ $msg }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form autocomplete="off" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <div class="row">
                <!-- Profile Photo Card -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Profile Photo</h4>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img id="profilePreview" @if(!empty($user->photo))
                                src="{{ asset('storage/profile/'.$user->photo) }}"
                                @else
                                src="https://www.pngfind.com/pngs/m/610-6104451_image-placeholder-png-user-profile-placeholder-image-png.png"
                                @endif
                                alt="Profile Image"
                                class="rounded-circle"
                                width="130"
                                height="130"
                                style="object-fit: cover;">
                            </div>
                            <h4 class="mb-1">{{ $user->firstname.' '.$user->lastname }}</h4>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <span class="badge bg-primary">{{ ucfirst($user->role->name) }}</span>

                            <hr class="my-3">

                            <p class="text-muted small mb-2">Max Size: 2MB | Formats: JPEG, JPG, PNG</p>
                            <div class="mb-3">
                                <input type="file" name="photo" id="image" onchange="showPreview(event)"
                                    class="form-control form-control-sm">
                            </div>
                            <button onclick="clearProfilePic(event)" type="button"
                                class="btn btn-sm btn-outline-danger">
                                <i class="fa fa-trash me-1"></i> Remove Photo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Personal Details Card -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Personal Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="firstname" id="firstname" placeholder="First Name"
                                        value="{{ !empty($user->firstname) ? $user->firstname : old('firstname') }}"
                                        class="form-control" readonly>
                                    @error('firstname') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="lastname" id="lastname" placeholder="Surname"
                                        value="{{ !empty($user->lastname) ? $user->lastname : old('lastname') }}"
                                        class="form-control" readonly>
                                    @error('lastname') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Other Names</label>
                                    <input type="text" name="othername" id="othername" placeholder="Other Name"
                                        value="{{ !empty($user->othername) ? $user->othername : old('othername') }}"
                                        class="form-control">
                                    @error('othername') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" placeholder="Phone Number"
                                        value="{{ !empty($user->phone) ? $user->phone : old('phone') }}"
                                        class="form-control">
                                    @error('phone') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="email" placeholder="Email"
                                        value="{{ !empty($user->email) ? $user->email : old('email') }}"
                                        class="form-control">
                                    @error('email') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Card -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Change Password</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('New Password') }}</label>
                                    <input type="password" name="password" id="password"
                                        placeholder="Enter new password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="new-password">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="password_confirmation"
                                        placeholder="Confirm new password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="d-flex justify-content-end gap-2 mb-2">
                        @if($user->nin_verified != 1)
                        <button type="submit" class="btn btn-primary btn-md">
                            <i class="fa fa-save me-1"></i> Save Changes
                        </button>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-danger btn-md">
                            <i class="fa fa-times me-1"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showPreview(event) {
    if (event.target.files.length > 0) {
        const src = URL.createObjectURL(event.target.files[0]);
        const preview = document.getElementById("profilePreview");
        preview.src = src;
    }
}

function clearProfilePic(event) {
    event.preventDefault();

    document.getElementById('image').value = '';

    document.getElementById("profilePreview").src =
        'https://www.pngfind.com/pngs/m/610-6104451_image-placeholder-png-user-profile-placeholder-image-png.png';
}
</script>
@endsection