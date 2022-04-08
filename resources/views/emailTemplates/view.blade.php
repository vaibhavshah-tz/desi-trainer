{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
    {{ Breadcrumbs::render('view-email-templates-title', $emailTemplate) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Profile Card-->
<div class="card card-custom card-stretch">
    <div class="card-header">
        @component('components.setActionBtn')
            @slot('title')
                {{ __('View email template') }}
            @endslot
            @slot('editRoute')
                {{ route('emailtemplate.edit', $emailTemplate->id) }}
            @endslot
            @slot('cancelRoute')
                {{ route('emailtemplate.index') }}
            @endslot
        @endcomponent
    </div>
    <div class="card-body py-4">
        <div class="form-group row my-2">
            <label class="col-4 col-form-label">{{ __('Name:') }}</label>
            <div class="col-8">
                <span class="form-control-plaintext font-weight-bolder">{{ $emailTemplate->name ?? config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
        <div class="form-group row my-2">
            <label class="col-4 col-form-label">{{ __('Subject:') }}</label>
            <div class="col-8">
                <span class="form-control-plaintext font-weight-bolder">{{ $emailTemplate->subject ?? config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
        <div class="form-group row my-2">
            <label class="col-4 col-form-label">{{ __('Slug:') }}</label>
            <div class="col-8">
                <span class="form-control-plaintext font-weight-bolder">{{ $emailTemplate->slug ?? config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
        <div class="form-group row my-2">
            <label class="col-4 col-form-label">{{ __('Body:') }}</label>
            <div class="col-8">
                <span class="form-control-plaintext font-weight-bolder">{!! $emailTemplate->body ?? config('constants.DEFAULT_MSG') !!}</span>
            </div>
        </div>
        <div class="form-group row my-2">
            <label class="col-4 col-form-label">{{ __('Keywords:') }}</label>
            <div class="col-8">
                @forelse($emailTemplate->keywords as $keyword)
                    <span class="form-control-plaintext font-weight-bolder">{{ $keyword['key'] ?? config('constants.DEFAULT_MSG') }} : {{ $keyword['description'] ?? config('constants.DEFAULT_MSG') }}</span>
                @empty
                    {{ config('constants.DEFAULT_MSG') }}
                @endforelse
            </div>
        </div>
    </div>
    <!--end::Body-->
</div>

@endsection

{{-- Style Section --}}
@section('styles')
@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection