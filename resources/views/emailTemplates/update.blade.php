{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
    {{ Breadcrumbs::render('edit-email-templates-title', $emailTemplate) }}
@endsection

{{-- Content --}}
@section('content')

<div class="card card-custom">
    <div class="card-header">
        @component('components.setActionBtn')
            @slot('title')
                {{ __('Edit email template') }}
            @endslot
            @slot('viewRoute')
                {{ route('emailtemplate.view', $emailTemplate->id) }}
            @endslot
            @slot('cancelRoute')
                {{ route('emailtemplate.index') }}
            @endslot
        @endcomponent
    </div>
    {{ Form::model($emailTemplate, ['route' => ['emailtemplate.update', $emailTemplate->id], 'class' => 'form', 'method' => 'PUT', 'id' => 'email-template-form']) }}
        @include('emailTemplates.form')
    {{ Form::close() }}
</div>

@endsection