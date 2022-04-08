@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('admin-personal-info') }}
@endsection

@section('content')

<div class="d-flex flex-row">
    {{-- Profile aside menu --}}
    @include('layout.base._profile-aside')

    <div class="flex-row-fluid ml-lg-8">
        <div class="card card-custom card-stretch">
            <div class="card-header py-3">
                <div class="card-title align-items-start flex-column">
                    <h3 class="card-label font-weight-bolder text-dark">Personal Information</h3>
                    <span class="text-muted font-weight-bold font-size-sm mt-1">Update your personal informaiton</span>
                </div>
                <div class="card-toolbar">
                    <button class="btn btn-success mr-2" onclick="event.preventDefault(); ($('#profile-update-form').valid()) ?
                            document.getElementById('profile-update-form').submit() : '';" data-toggle="tooltip" data-theme="dark" title="{{ __('Save changes') }}">
                        {{ __('Save changes') }}</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary" data-toggle="tooltip" data-theme="dark" title="{{ __('Cancel') }}">{{__('Cancel') }}</a>
                </div>
            </div>
            {{ Form::model(Auth::user(), ['route' => ['admin.update-profile'], 'method' => 'post','id' => 'profile-update-form','class' => 'form', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" value="{{Auth::user()->id}}" id="user-id">
            <div class="card-body">
                <!-- <div class="row">
                    <label class="col-xl-3"></label>
                    <div class="col-lg-9 col-xl-6">
                        <h5 class="font-weight-bold mb-6">Personal Info</h5>
                    </div>
                </div> -->
                <!-- <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Avatar</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="image-input image-input-outline" id="kt_profile_avatar" style="background-image: url({{config('constants.IMAGE_PATH.DEFAULT_AVATAR')}})">
                            <div class="image-input-wrapper" style="background-image: url({{ asset(Auth::user()->avatar_url ?? '') }})"></div>
                            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                                <i class="fa fa-pen icon-sm text-muted"></i>
                                <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                <input type="hidden" name="avatar_remove" />
                            </label>
                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                                <i class="ki ki-bold-close icon-xs text-muted"></i>
                            </span>
                            @if(!empty(Auth::user()->avatar))
                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
                                <i class="ki ki-bold-close icon-xs text-muted"></i>
                            </span>
                            @endif
                        </div>
                        <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                    </div>
                </div> -->
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">First Name</label>
                    <div class="col-lg-9 col-xl-6">
                        <input class="form-control form-control-lg form-control-solid @error('first_name') is-invalid @enderror" name="first_name" type="text" value="{{Auth::user()->first_name ?? ''}}" />
                        @error('first_name')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Last Name</label>
                    <div class="col-lg-9 col-xl-6">
                        <input class="form-control form-control-lg form-control-solid @error('last_name') is-invalid @enderror" name="last_name" type="text" value="{{Auth::user()->last_name ?? ''}}" />
                        @error('last_name')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                </div>
                <!-- <div class="row">
                    <label class="col-xl-3"></label>
                    <div class="col-lg-9 col-xl-6">
                        <h5 class="font-weight-bold mt-10 mb-6">Contact Info</h5>
                    </div>
                </div> -->
                <!-- <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Country Code</label>
                    <div class="col-lg-9 col-xl-6">
                        {{ Form::select('country_code', App\Models\Country::getAllCountryCode(), null, ['class' => 'form-control form-control-lg form-control-solid select2 '.($errors->has('country_code') ? 'is-invalid' : ''), 'placeholder' => 'Select country code', 'id' => 'country_code']) }}
                        @error('country_code')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                </div> -->
                {{ Form::hidden('country_code', null, ['id' => 'country_code']) }}
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Contact Number</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg @error('phone_number') is-invalid @enderror">
                            <input type="text" class="form-control form-control-lg" name="phone_number" id="phone_number" value="{{Auth::user()->full_phone_number ?? ''}}" placeholder="Phone" />
                        </div>
                        @error('phone_number')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                        @error('full_phone_number')
                            @component('components.serverValidation')
                                {{ $message }}
                            @endcomponent
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Email Address</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid @error('email') is-invalid @enderror">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="la la-at"></i>
                                </span>
                            </div>
                            <input type="email" class="form-control form-control-lg form-control-solid" id="user-email" name="email" value="{{Auth::user()->email ?? ''}}" placeholder="Email" />
                        </div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@endsection

{{-- Style Section --}}
@section('styles')
<link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet" type="text/css" />
<style>
    .iti {display: inherit;}
    .iti__flag {background-image: url("{{ asset('media/flags.png') }}");}

    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .iti__flag {background-image: url("{{ asset('media/flags@2x.png') }}");}
    }
</style>
@endsection

@section('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/admin.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/intlTelInput.js') }}" type="text/javascript"></script>
<script>
    // var avatar = new KTImageInput('kt_profile_avatar');

    // avatar.on('remove', function(imageInput) {
    //     $('input[type=hidden][name=avatar_remove]').val('1');
    // });
    createPhoneNumberInput('#phone_number'); // Pass id of the input

    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection