@extends('layout.login')

@section('content')

<div>
    @if (session('status'))
    <div class="alert alert-danger" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <div class="mb-15">
        <h3 class="opacity-40 font-weight-normal">Sign In To Admin</h3>
        <!-- <p class="opacity-60 font-weight-bold">Enter your details to login to your account:</p> -->
    </div>
    <form class="form" id="login-form" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <input class="form-control h-auto text-white bg-white-o-5 rounded-pill border-0 py-4 px-8
            @error('email') is-invalid @enderror" id="email" type="email" placeholder="Email" name="email" value="{{ old('email') }}" autocomplete="of" autofocus />

            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <input class="form-control h-auto text-white bg-white-o-5 rounded-pill border-0 py-4 px-8
            @error('password') is-invalid @enderror" id="password" type="password" placeholder="Password" name="password" />

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group d-flex flex-wrap justify-content-between align-items-center px-8 opacity-60">
            <div class="checkbox-inline">
                <label class="checkbox checkbox-outline checkbox-white text-white m-0" data-toggle="tooltip" data-theme="light" title="{{ __('Remember me') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                    <span></span>{{ __('Remember me') }}</label>
            </div>

            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" id="kt_login_forgot" class="text-white font-weight-bold" data-toggle="tooltip" data-theme="light" title="{{ __('Forget Password ?') }}">{{ __('Forget Password ?') }}</a>
            @endif
        </div>
        <div class="form-group text-center mt-10">
            <button type="submit" class="btn btn-pill btn-primary opacity-90 px-15 py-3" data-toggle="tooltip" data-theme="light" title="{{ __('Sign In') }}">{{ __('Sign In') }}</button>
        </div>
    </form>
</div>

@endsection
@section('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/login.js') }}" type="text/javascript"></script>
@endsection