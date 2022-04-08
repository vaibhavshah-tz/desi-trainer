{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('trainer-view-title', $trainer) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Profile Card-->
<div class="">
    <div class="card card-custom card-stretch">
        <div class="card-header">
            @component('components.setActionBtn')
            @slot('title')
            View trainer details
            @endslot
            @slot('editRoute')
            {{ route('trainer.edit', $trainer->id) }}
            @endslot
            @slot('deleteRoute')
            {{ route('trainer.delete', $trainer->id) }}
            @endslot
            @slot('cancelRoute')
            {{ route('trainer.index') }}
            @endslot
            @endcomponent
        </div>

        <div class="card-body py-4">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                    <div class="symbol-label" style="background-image:url({{asset($trainer->avatar_url)}});width: 80px;height:80px;"></div>
                </div>
                @php
                $gender = CommonHelper::genderLabel($trainer->gender);
                $status = CommonHelper::trainerStatusLabel($trainer->status);
                @endphp
                <div>
                    <a href="javascript:void(0)" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $trainer->full_name ?? config('constants.DEFAULT_MSG') }}</a>
                    <div class="text-muted">{{ $trainer->gender == config('constants.GENDER.MALE') ? config('constants.GENDER.MALE_LABEL') : config('constants.GENDER.FEMALE_LABEL')}}</div>
                </div>
            </div>
            <div class="row mt-5 mb-5 view-detail">
                <div class="col-md-6 border-rght">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Personal Info') }}</h5>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('First name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->first_name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Last name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->last_name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Email:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->email ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <!-- <div class="form-group row my-0">
                        <label class="col-4 col-form-label">{{ __('Username:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext font-weight-bolder">{{ $trainer->username ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div> -->
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Gender:') }}</label>
                        <div class="col-8 d-flex align-items-center flex-wrap">
                            <span class="form-control-plaintext {{$gender['class']}}">
                                {{ $gender['title'] ?? config('constants.DEFAULT_MSG') }}
                            </span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Status:') }}</label>
                        <div class="col-8 d-flex align-items-center flex-wrap">
                            <span class="form-control-plaintext {{$status['class'] ?? ''}}">
                                {{ $status['title'] ?? config('constants.DEFAULT_MSG') }}
                            </span>
                        </div>
                        <!-- <div class="col-8 d-flex align-items-center">
                            @php
                            $checked = ($trainer->status) ? 'checked' : '';
                            @endphp
                            <input data-switch="true" id="status-change" type="checkbox" {{$checked}} data-on-text="{{ config('constants.ACTIVE_LABEL') }}" data-handle-width="60" data-off-text="{{ config('constants.INACTIVE_LABEL') }}" data-on-color="primary" />
                        </div> -->
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Note:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->note ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Contact Info') }}</h5>
                    @if(Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN'))
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Country code:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->country_code ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Phone number:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->phone_number ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Country:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->country->name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('City:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->city ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Zipcode:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->zipcode ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Timezone:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $trainer->timezone->label ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Body-->
        </div>

        <div class="card card-custom card-stretch view-detail">
            <div class="card-body py-4">
                <h5 class="text-dark font-weight-bold mb-2">{{ __('Your skills') }}</h5>
                <div class="row mt-5 mb-5 view-detail">
                    <div class="col-md-6 border-rght">
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Title:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ $trainer->skill_title ?? config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Course Category:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ $trainer->courseCategory->name ?? config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Total Experience Month:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ $trainer->total_experience_month ?? config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Total Experience Year:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ $trainer->total_experience_year ?? config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>

                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Resume:') }}</label>
                            <div class="col-4">
                                @if($trainer->resume_url)
                                <a href="{{ $trainer->resume_url }}" id="view_resume" target="_blank" class="btn btn-light-primary font-weight-bold form-control p-0 d-flex align-items-center justify-content-center" data-toggle="tooltip" data-theme="dark" title="{{ __('View Resume') }}" download>{{ __('View Resume') }}</a>
                                @else
                                {{ config('constants.DEFAULT_MSG') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Prior Teaching Experience Month:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ $trainer->prior_teaching_experience_month ?? config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Prior Teaching Experience Year:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ $trainer->prior_teaching_experience_year ?? config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>

                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Interested in:') }}</label>
                            <div class="col-8 d-flex align-items-center flex-wrap">
                                @if($trainer->ticketTypes)
                                @foreach($trainer->ticketTypes as $value)
                                <span class="form-control-plaintext label label-dark label-inline mr-1 mb-1">
                                    {{ $value['name'] }}
                                </span>
                                @endforeach
                                @else
                                {{ config('constants.DEFAULT_MSG') }}
                                @endif
                            </div>
                        </div>
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Primary Skills:') }}</label>
                            <div class="col-8 d-flex align-items-center flex-wrap">
                                @if($trainer->primarySkills)
                                @foreach($trainer->primarySkills as $value)
                                <span class="form-control-plaintext label label-dark label-inline mr-1 mb-1">
                                    {{ $value['name'] }}
                                </span>
                                @endforeach
                                @else
                                {{ config('constants.DEFAULT_MSG') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row my-0 trainer-comment">
                    <div class="col-md-6">
                        <label class="col-form-label font-weight-bolder">{{ __('Comments for Trainer:') }}</label>
                        <table class="table">
                            @forelse($trainer->trainerComments as $value)
                            <tr>
                                <th width="150">{{ $value->user->full_name ?? '' }}</th>
                                <th>{{ $value->note ?? '' }}</th>
                            </tr>
                            @empty
                            <tr>
                                <th width="150"></th>
                                <th>{{ __('No comments found') }}</th>
                            </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
            <!--end::Body-->
        </div>
        <div class="card card-custom card-stretch view-detail">
            <div class="card-body py-4">
                <h5 class="text-dark font-weight-bold mb-2">{{ __('Price') }}</h5>
                <div class="row mt-5 mb-5 view-detail">
                    <div class="col-md-6 border-rght">
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Training:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ ($trainer->training_price) ? CommonHelper::formatPrice($trainer->training_price) : config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Job Support:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ ($trainer->job_support_price) ? CommonHelper::formatPrice($trainer->job_support_price) : config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>
                        <div class="form-group row my-0">
                            <label class="col-4 col-form-label font-weight-bolder">{{ __('Interview Support:') }}</label>
                            <div class="col-8">
                                <span class="form-control-plaintext">{{ ($trainer->interview_support_price) ? CommonHelper::formatPrice($trainer->interview_support_price) : config('constants.DEFAULT_MSG') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Body-->
        </div>
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
                    type: 'POST',
                    url: "{{ route('trainer.update.status') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        status: e.target.checked,
                        id: "{{$trainer->id}}"
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