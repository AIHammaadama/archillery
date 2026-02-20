@extends('layouts.fullwidth')

@section("title")
Account Verification | PPMS
@endsection

@section('content')

<div class="bg-gradient-success p-5 rounded" style="margin-top: 120px; margin-bottom: 100px;">
    <h1 class="text-center">Email Verification</h1>
    @if (session('resent'))
    <div class="alert alert-success" role="alert">
        A fresh verification link has been sent to your email address.
    </div>
    @endif
    <div class="col-xl-12">
        <div class="alert alert-primary left-icon-big alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span></span>
            </button>
            <div class="media">
                <div class="alert-left-icon-big">
                    <span><i class="mdi mdi-email-alert"></i></span>
                </div>
                <div class="media-body">
                    <h4 class="mt-1 mb-2">Welcome to your account!</h4>
                    <p class="mb-0" style="font-size: 18px;">
                        Before proceeding, please check your email for a verification link.
                    <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
                        @csrf
                        <br>
                        <button type="submit" class="btn btn-primary" style="font-size: 18px;">
                            Resend Email
                        </button>.
                    </form>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection