{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
    {{ Breadcrumbs::render('edit-customers-title', $user) }}
@endsection

{{-- Content --}}
@section('content')

<div class="card card-custom">
    <div class="card-header">
        @component('components.setActionBtn')
            @slot('title')
                {{ __('Edit Customer') }}
            @endslot
            @slot('viewRoute')
                {{ route('customer.view', $user->id) }}
            @endslot
            @slot('deleteRoute')
                {{ route('customer.delete', $user->id) }}
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
        <div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="step-first" data-wizard-clickable="true">
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
                </div>
            </div>
            <div class="row justify-content-center py-10 px-8 py-lg-12 px-lg-10">
                <div class="col-xl-12 col-xxl-7">
                    {{ Form::model($user, ['route' => ['customer.update', $user->id], 'class' => 'form', 'method' => 'PUT', 'id' => 'kt_form', 'files' => true]) }}
                        @include('customers.form')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection