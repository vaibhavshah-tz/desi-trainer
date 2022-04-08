{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('tickets-view-title', $ticketDetails) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Card-->
<div>
    <div class="card card-custom card-stretch">
        <div class="card-header">
            @component('components.setActionBtn')
            @slot('title')
            View Ticket Details
            @endslot
            @slot('editRoute')
            {{ route('tickets.edit', $ticketDetails->id) }}
            @endslot
            @endcomponent
        </div>
        <!-- Course Details Section -->
        <div class="card-body py-4">
            <div class="row mt-5 mb-5 view-detail">
                <div class="col-md-6 border-rght">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Basic info') }}</h5>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Ticket type:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->ticketType->name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Date:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->date_formated ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Time:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->time_formated ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Timezone:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->timezone->label ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Is for employee:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ ($ticketDetails->is_for_employee) ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Status:') }}</label>
                        <div class="col-8">
                            <span class="label label-lg text-left label-inline {{CommonHelper::getTicketStatusLabel()[$ticketDetails->status]['class']}}">
                                {{CommonHelper::getTicketStatusLabel()[$ticketDetails->status]['title'] ?? config('constants.DEFAULT_MSG')}}
                            </span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Assigned admin:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->user->full_name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Is set global:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ ($ticketDetails->is_global) ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Message added by customer:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{!! !empty($ticketDetails->message) ? nl2br($ticketDetails->message) : config('constants.DEFAULT_MSG') !!}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Course info') }}</h5>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Course category:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->courseCategory->name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Course:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->course->name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Primary skills:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">
                                @if($ticketDetails->primarySkills->count() > 0)
                                {{ $ticketDetails->primarySkills->implode('name', ', ') }}
                                @else
                                {{ config('constants.DEFAULT_MSG') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Other course category:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->other_course_category ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Other course:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->other_course ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Other primary skill:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->other_primary_skill ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mt-5 mb-5 view-detail">
                <div class="col-md-6 border-rght">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Customer info') }}</h5>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->customer->full_name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Email:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $ticketDetails->customer->email ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('User Type:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ CommonHelper::getCustomerType()[$ticketDetails->customer->user_type] ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Budget:') }}</label>
                        <div class="col-8">
                            @if(CommonHelper::formatPrice($ticketDetails->customer_budget))
                                <span class="label label-lg font-weight-bold label-light-info label-inline">{{ CommonHelper::formatPrice($ticketDetails->customer_budget) ?? config('constants.DEFAULT_MSG') }}</span>
                            @else
                                {{ config('constants.DEFAULT_MSG') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Assigned trainer') }}</h5>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ !empty($ticketDetails->trainer->full_name) ? $ticketDetails->trainer->full_name : config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Final pricing:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{!! ($ticketDetails->trainer && $ticketDetails->trainer->quotes->count() > 0) ? @$ticketDetails->trainer->quotes->first()->quote_label : config('constants.DEFAULT_MSG') !!}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($ticketDetails->is_for_employee && $ticketDetails->ticketEmployees->count() > 0)
        <div class="card-body">
            <h5 class="text-dark font-weight-bold mb-2">{{ __('Employee info') }}</h5>
            @foreach($ticketDetails->ticketEmployees as $key => $value)
            <div class="row {{($loop->first) ? 'mt-10' : ''}} mb-5 view-detail">
                <div class="form-group my-0 col-lg-4 praposal-div">
                    <label class="col-4 pl-0 font-weight-bolder">{{ __('Name:') }}</label>
                    <div class="col-8">
                        <span class="">{{ $value->employee_name ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
                <div class="form-group my-0 col-lg-4 praposal-div">
                    <label class="col-4 pl-0 font-weight-bolder">{{ __('Email:') }}</label>
                    <div class="col-8">
                        <span class="">{{ $value->email ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
                <div class="form-group my-0 col-lg-4 praposal-div">
                    <label class="col-4 pl-0 font-weight-bolder">{{ __('Phone:') }}</label>
                    <div class="col-8">
                        <span class="">{{ $value->full_phone_number ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
            </div>
            @if(!$loop->last)
            <hr>
            @endif
            @endforeach
        </div>
        @endif
        <!--end::Body-->
    </div>
</div>
<!--end::Card-->
@endsection

{{-- Style Section --}}
@section('styles')
@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection