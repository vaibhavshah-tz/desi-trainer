@extends('layout.login')

@section('content')

<div>
    @if (session('status'))
    <div class="alert alert-danger" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <div class="mb-20">
        <h3 class="opacity-40 font-weight-normal">Forgotten Password ?</h3>
        <p class="opacity-40">Enter your email to reset your password</p>
    </div>
    <form class="form" id="forgot-password-form" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group mb-10">
            <input class="form-control h-auto text-white bg-white-o-5 rounded-pill border-0 py-4 px-8
             @error('email') is-invalid @enderror" type="email" id="email" placeholder="Email" name="email" autocomplete="off" value="{{ old('email') }}" autofocus />

            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <button id="kt_login_forgot_submit" class="btn btn-pill btn-primary opacity-90 px-15 py-3 m-2" type="submit" data-toggle="tooltip" data-theme="light" title="{{ __('Request') }}">{{ __('Request') }}</button>
            <a href="{{ route('login') }}" id="kt_login_forgot_cancel" class="btn btn-pill btn-outline-white opacity-70 px-15 py-3 m-2" data-toggle="tooltip" data-theme="light" title="{{ __('Cancel') }}">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>

@endsection
@section('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/login.js') }}" type="text/javascript"></script>
@endsection