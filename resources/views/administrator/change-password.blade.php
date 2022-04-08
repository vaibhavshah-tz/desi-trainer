@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('admin-change-password') }}
@endsection

@section('content')

<div class="d-flex flex-row">
    {{-- Profile aside menu --}}
    @include('layout.base._profile-aside')

    <div class="flex-row-fluid ml-lg-8">
        <div class="card card-custom">
            <div class="card-header py-3">
                <div class="card-title align-items-start flex-column">
                    <h3 class="card-label font-weight-bolder text-dark">Change Password</h3>
                    <span class="text-muted font-weight-bold font-size-sm mt-1">Change your account password</span>
                </div>
                <div class="card-toolbar">
                    <button class="btn btn-success mr-2" onclick="event.preventDefault(); ($('#change-password-form').valid()) ?
                            document.getElementById('change-password-form').submit() : '';" data-toggle="tooltip" data-theme="dark" title="{{ __('Save changes') }}">
                        {{ __('Save changes') }}</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary" data-toggle="tooltip" data-theme="dark" title="{{ __('Cancel') }}">{{ __('Cancel') }}</a>
                </div>
            </div>
            <form class="form" method="POST" action="{{route('admin.update-password')}}" id="change-password-form">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Current Password</label>
                        <div class="col-lg-9 col-xl-6">
                            <input type="password" name="old_password" class="form-control form-control-lg form-control-solid mb-2 @error('old_password') is-invalid @enderror" value="" placeholder="Current password" autocomplete="false" />
                            @error('old_password')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">New Password</label>
                        <div class="col-lg-9 col-xl-6">
                            <input type="password" name="new_password" id="new-password" class="form-control form-control-lg form-control-solid @error('new_password') is-invalid @enderror" value="" placeholder="New password" autocomplete="false" />
                            @error('new_password')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label text-alert">Verify Password</label>
                        <div class="col-lg-9 col-xl-6">
                            <input type="password" name="password_confirmation" class="form-control form-control-lg form-control-solid @error('password_confirmation') is-invalid @enderror" value="" placeholder="Verify password" autocomplete="false" />
                            @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/admin.js') }}" type="text/javascript"></script>
@endsection