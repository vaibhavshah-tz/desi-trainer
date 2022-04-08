{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('view-customers-title', $user) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Profile Card-->
<div class="">
    <div class="card card-custom card-stretch">
        <div class="card-header">
            @component('components.setActionBtn')
                @slot('title')
                    {{ __('View customer') }}
                @endslot
                @slot('editRoute')
                    {{ route('customer.edit', $user->id) }}
                @endslot
                @slot('deleteRoute')
                    {{ route('customer.delete', $user->id) }}
                @endslot
                @slot('cancelRoute')
                    {{ route('customer.index') }}
                @endslot
            @endcomponent
        </div>
        <div class="card-body py-4">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                    <div class="symbol-label" style="background-image:url({{asset($user->avatar_url)}});width: 80px;height: 80px;"></div>
                </div>
                @php
                $gender = CommonHelper::genderLabel($user->gender);
                @endphp
                <div>
                    <a href="javascript:void(0)" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $user->full_name ?? config('constants.DEFAULT_MSG') }}</a>
                    <div class="text-muted">{{ $user->gender == config('constants.GENDER.MALE') ? config('constants.GENDER.MALE_LABEL') : config('constants.GENDER.FEMALE_LABEL')}}</div>
                </div>
            </div>

            <div class="row mt-5 mb-5 view-detail">
                <div class="col-md-6 border-rght">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Personal Info') }}</h5>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('First Name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->first_name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Last Name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->last_name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Email:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->email ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <!-- <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{ __('Username:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder">{{ $user->username ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div> -->
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Gender:') }}</label>
                        <div class="col-8 d-flex align-items-center flex-wrap">
                            <span class="form-control-plaintext {{ CommonHelper::genderLabel($user->gender)['class'] }}">{{ CommonHelper::genderLabel($user->gender)['title'] ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('User Type:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ CommonHelper::getCustomerType()[$user->user_type] ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Company Name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->company_name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <!-- <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{ __('Company Address:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder">{!! !empty($user->company_address) ? nl2br($user->company_address) : config('constants.DEFAULT_MSG') !!}</span>
                        </div>
                    </div> -->
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Status:') }}</label>
                        <div class="col-8 d-flex align-items-center">
                            @php
                            $checked = ($user->status) ? 'checked' : '';
                            @endphp
                            <input data-switch="true" id="status-change" type="checkbox" {{$checked}} data-on-text="{{ config('constants.ACTIVE_LABEL') }}" data-handle-width="60" data-off-text="{{ config('constants.INACTIVE_LABEL') }}" data-on-color="primary" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Contact Info') }}</h5>
                    @if(Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN'))
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Country code:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->country_code ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Phone number:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->phone_number ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Country:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->country->name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('City:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->city ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Zipcode:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->zipcode ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Timezone:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $user->timezone->label ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Body-->
    </div>
</div>
@endsection

{{-- Style Section --}}
@section('styles')
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/pages/crud/forms/widgets/bootstrap-switch.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        // Update the status
        $('#status-change').on('switchChange.bootstrapSwitch', function(e, state) {
            $.ajax({
                type: 'POST',
                url: "{{ route('customer.update.status') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    status: e.target.checked,
                    id: "{{$user->id}}"
                },
                success: function(data) {
                    toastr.success("Status has been updated.");
                },
                error: function(data) {
                    toastr.error("Status has not been updated.");
                }
            });
        });
    });
</script>
@endsection