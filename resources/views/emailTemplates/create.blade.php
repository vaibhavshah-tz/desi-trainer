{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
    {{ Breadcrumbs::render('add-email-templates') }}
@endsection

{{-- Content --}}
@section('content')

<div class="card card-custom">
    <div class="card-header">
        @component('components.setActionBtn')
            @slot('title')
                {{ __('Create new email template') }}
            @endslot
            @slot('cancelRoute')
                {{ route('emailtemplate.index') }}
            @endslot
        @endcomponent
    </div>
    {{ Form::open(['route' => 'emailtemplate.store', 'class' => 'form', 'id' => 'email-template-form']) }}
        @include('emailTemplates.form')
    {{ Form::close() }}
</div>

@endsection