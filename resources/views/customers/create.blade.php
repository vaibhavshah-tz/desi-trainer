{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
    {{ Breadcrumbs::render('add-customers') }}
@endsection

{{-- Content --}}
@section('content')

<div class="card card-custom">
    <div class="card-header">
        @component('components.setActionBtn')
            @slot('title')
                {{ __('Create new customer') }}
            @endslot
            @slot('cancelRoute')
                {{ route('customer.index') }}
            @endslot
        @endcomponent
    </div>
    <div class="card-body p-0">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="wizard wizard-3 wizard-form" id="kt_wizard_v3" data-wizard-state="step-first" data-wizard-clickable="true">
            <div class="wizard-nav">
                <div class="wizard-steps px-8 py-8 px-lg-15 py-lg-3">
                    <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                        <div class="wizard-label">
                            <h3 class="wizard-title">
                            <span>1.</span>Personal Info</h3>
                            <div class="wizard-bar"></div>
                        </div>
                    </div>
                    <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-label">
                            <h3 class="wizard-title">
                            <span>2.</span>Contact Info</h3>
                            <div class="wizard-bar"></div>
                        </div>
                    </div>
                    <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-label">
                            <h3 class="wizard-title">
                            <span>3.</span>Create Password</h3>
                            <div class="wizard-bar"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pl-10 pr-10 pb-10">
                <div class="">
                    {{ Form::open(['route' => 'customer.store', 'class' => 'form', 'id' => 'kt_form', 'files' => true]) }}
                        @include('customers.form')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection