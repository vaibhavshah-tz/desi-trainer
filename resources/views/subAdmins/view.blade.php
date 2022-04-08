{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('sub-admin-view-title', $user) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Profile Card-->
<div class="card card-custom card-stretch">
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        View sub admin
        @endslot
        @permission('edit-sub-admin')
        @slot('editRoute')
        {{ route('subadmin.edit', $user->id) }}
        @endslot
        @endpermission
        @permission('delete-sub-admin')
        @slot('deleteRoute')
        {{ route('subadmin.delete', $user->id) }}
        @endslot
        @endpermission
        @permission('sub-admin-list')
        @slot('cancelRoute')
        {{ route('subadmin.index') }}
        @endslot
        @endpermission
        @endcomponent
    </div>
    <div class="card-body py-4">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                <div class="symbol-label" style="background-image:url({{asset($user->avatar_url)}});width: 80px;height: 80px;"></div>
            </div>
            @php
            $role = CommonHelper::roleLabel($user->role_id);
            @endphp
            <div>
                <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $user->full_name ?? config('constants.DEFAULT_MSG') }}</a>
                <div class="text-muted">{{ $user->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN') ? config('constants.ADMIN_ROLE.SUPER_ADMIN_LABEL') : config('constants.ADMIN_ROLE.SUB_ADMIN_LABEL')}}</div>
            </div>
        </div>
        <div class="row mt-5 mb-5 view-detail"> 
            <div class="col-md-6 border-rght">
                <div class="form-group row my-0">
                    <label class="col-4 col-form-label font-weight-bolder">{{ __('First name:') }}</label>
                    <div class="col-8">
                        <span class="form-control-plaintext">{{ $user->first_name ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
                <div class="form-group row my-0">
                    <label class="col-4 col-form-label font-weight-bolder">{{ __('Email:') }}</label>
                    <div class="col-8">
                        <span class="form-control-plaintext">{{ $user->email ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
                <div class="form-group row my-0">
                    <label class="col-4 col-form-label font-weight-bolder">{{ __('Role:') }}</label>
                    <div class="col-8 d-flex align-items-center flex-wrap">
                        <span class="form-control-plaintext {{$role['class']}}">
                            {{ $role['title'] ?? config('constants.DEFAULT_MSG') }}
                        </span>
                    </div>
                </div>
                @if($user->role_id == config('constants.ADMIN_ROLE.SUB_ADMIN'))
                <div class="form-group row my-0">
                    <label class="col-4 col-form-label font-weight-bolder">{{ __('Status:') }}</label>
                    <div class="col-8 d-flex align-items-center">
                        @php
                        $checked = ($user->status) ? 'checked' : '';
                        @endphp
                        <input data-switch="true" id="status-change" type="checkbox" {{$checked}} data-on-text="{{ config('constants.ACTIVE_LABEL') }}" data-handle-width="60" data-off-text="{{ config('constants.INACTIVE_LABEL') }}" data-on-color="primary" />
                    </div>
                </div>
                @endif
            </div>
            <div class="col-md-6">
                <div class="form-group row my-0">
                    <label class="col-4 col-form-label font-weight-bolder">{{ __('Last name:') }}</label>
                    <div class="col-8">
                        <span class="form-control-plaintext">{{ $user->last_name ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
                <div class="form-group row my-0">
                    <label class="col-4 col-form-label font-weight-bolder">{{ __('Phone number:') }}</label>
                    <div class="col-8">
                        <span class="form-control-plaintext">{{ $user->phone_number ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
                <div class="form-group row my-0">
                    <label class="col-4 col-form-label font-weight-bolder">{{ __('Call Support:') }}</label>
                    <div class="col-8">
                        <span class="form-control-plaintext">
                            {{ $user->call_support ? 'Yes': 'No' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Body-->
</div>
<!--end::Profile Card-->

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
                //data: $("#registerSubmit").serialize(),
                type: 'POST',
                url: "{{ route('subadmin.update.status') }}",
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