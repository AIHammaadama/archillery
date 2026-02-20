@extends('layouts.guest')

@section("title")
Login | PPMS
@endsection

@section('content')
<div class="container-fluid min-vh-100 p-0">
    <div class="row min-vh-100 g-0">
        {{-- LEFT PANEL --}}
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center login-bg">
            <div class="text-center px-5">
                <br><br><br><br><br>
                <img src="{{ asset('/images/logo.png') }}" class="mb-3" style="width: 220px;">
                <img src="{{ asset('/images/logo-text.png') }}" style="width: 300px;">
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-info-light login-bg-right">
            <div class="w-100" style="max-width: 400px;">
                <h3 class="text-center mb-4" style="margin-top: 100px;">Welcome back</h3>
                <p class="text-white text-center">Sign In to PPMS - Built for efficiency, transparency, and control.
                </p>
                @if($msg = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ $msg }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                @endif

                <div class="card shadow-sm login-card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" placeholder="Enter your email" required
                                    autofocus>
                                @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" placeholder="Enter your password" required>
                                @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3 text-end">
                                @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-secondary"
                                    style="font-size: 14px;">
                                    Forgot password?
                                </a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-secondary btn-lg w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
                <small class="text-dark text-center d-block mt-3 mb-3">
                    Secure access • Authorized personnel only
                </small>
                <small class="text-dark text-center d-block mb-2">
                    &copy; {{ date("Y") }}. AMS Herald Project Procurement Management System. All rights reserved.
                </small>
            </div>
        </div>

    </div>
</div>
@endsection