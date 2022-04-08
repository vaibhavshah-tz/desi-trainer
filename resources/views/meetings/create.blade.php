{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('meeting-create', request()->route('id')) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        <h3 class="card-title">{{ __('Create New Meeting') }}</h3>
        <div class="card-toolbar">
            <a href="{{ route('meetings', request()->route('id')) }}" class="btn btn-light-success mr-2 btn-sm" data-toggle="tooltip" data-theme="dark" title="Back"><i class="flaticon-reply"></i>Back</a>
        </div>
    </div>
    {{ Form::open(['id' => 'create-meeting','class' => 'form']) }}
    @include('meetings._form')
    {{ Form::close() }}
</div>
<!--end::Card-->
@endsection