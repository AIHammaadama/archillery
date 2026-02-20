@extends('layouts.fullwidth')

@section("title")
Verify | PPMS
@endsection

@section('content')
<div class="container" style="margin-top: 120px; margin-bottom: 100px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="text-center mt-4 mb-4">&nbsp;</h3>
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                    <div class="alert alert-secondary" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                    @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection