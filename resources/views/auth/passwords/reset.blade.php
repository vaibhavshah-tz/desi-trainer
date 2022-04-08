@extends('layout.login')

@section('content')

<div>
    @if (session('status'))
    <div class="alert alert-danger" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <div class="mb-20">
        <h3 class="opacity-40 font-weight-normal">Reset Password</h3>
    </div>
    <form class="form" id="reset-password-form" method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-group">
            <input class="form-control h-auto text-white bg-white-o-5 rounded-pill border-0 py-4 px-8
            @error('password') is-invalid @enderror" id="password" type="password" placeholder="Password" name="password" />

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <input id="password-confirm" type="password" class="form-control h-auto text-white bg-white-o-5 rounded-pill border-0 py-4 px-8" name="password_confirmation" placeholder="Confirm Password" autocomplete="new-password">

        </div>

        <div class="form-group text-center mt-10">
            <button type="submit" class="btn btn-pill btn-primary opacity-90 px-15 py-3" data-toggle="tooltip" data-theme="light" title="{{ __('Reset Password') }}">{{ __('Reset Password') }}</button>
        </div>
    </form>
</div>

@endsection
@section('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/login.js') }}" type="text/javascript"></script>
@endsection