{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('trainer-create') }}
@endsection

{{-- Content --}}
@section('content')
<div class="card card-custom">
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        {{ __('Create new trainer') }}
        @endslot
        @slot('cancelRoute')
        {{ route('trainer.index') }}
        @endslot
        @endcomponent
    </div>
    <div class="card-body p-0">
        <!--begin: Wizard-->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="step-first" data-wizard-clickable="true">
            <!--begin: Wizard Nav-->
            <div class="wizard-nav">
                <div class="wizard-steps px-8 py-8 px-lg-15 py-lg-3">
                    <!--begin::Wizard Step 1 Nav-->
                    <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                        <div class="wizard-label">
                            <h3 class="wizard-title">
                                <span>1.</span>{{ __('Personal Info') }}</h3>
                            <div class="wizard-bar"></div>
                        </div>
                    </div>
                    <!--end::Wizard Step 1 Nav-->
                    <!--begin::Wizard Step 2 Nav-->
                    <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-label">
                            <h3 class="wizard-title">
                                <span>2.</span>{{ __('Contact Info') }}</h3>
                            <div class="wizard-bar"></div>
                        </div>
                    </div>
                    <!--end::Wizard Step 2 Nav-->
                    <!--begin::Wizard Step 3 Nav-->
                    <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-label">
                            <h3 class="wizard-title">
                                <span>3.</span>{{ __('Your Skills') }}</h3>
                            <div class="wizard-bar"></div>
                        </div>
                    </div>
                    <!--end::Wizard Step 3 Nav-->
                    <!--begin::Wizard Step 4 Nav-->
                    <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-label">
                            <h3 class="wizard-title">
                                <span>4.</span>{{ __('Price') }}</h3>
                            <div class="wizard-bar"></div>
                        </div>
                    </div>
                    <!--end::Wizard Step 4 Nav-->
                    <!--begin::Wizard Step 5 Nav-->
                    <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-label">
                            <h3 class="wizard-title">
                                <span>5.</span>{{ __('Create Password') }}</h3>
                            <div class="wizard-bar"></div>
                        </div>
                    </div>
                    <!--end::Wizard Step 5 Nav-->
                </div>
            </div>
            <!--end: Wizard Nav-->
            <!--begin: Wizard Body-->
            <div class="pl-10 pr-10 pb-10">               
                    <!--begin: Wizard Form-->
                    {{ Form::open(['id' => 'kt_form','class' => 'form trainer-add', 'enctype' => 'multipart/form-data' ]) }}
                    @include('trainer._form')
                    {{ Form::close() }}
                    <!--end: Wizard Form-->
            </div>
            <!--end: Wizard Body-->
        </div>
        <!--end: Wizard-->
    </div>
</div>
@endsection

