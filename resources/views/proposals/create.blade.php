{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('proposal-create', request()->route('id')) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        <h3 class="card-title">{{ __('Create New Proposal') }}</h3>
        <div class="card-toolbar">
            <a href="{{ route('proposals', request()->route('id')) }}" class="btn btn-light-success mr-2 btn-sm" data-toggle="tooltip" data-theme="dark" title="Back"><i class="flaticon-reply"></i>Back</a>
        </div>
    </div>
    {{ Form::open(['id' => 'proposal-form','class' => 'form']) }}
    @include('proposals._form')
    {{ Form::close() }}
</div>
<!--end::Card-->
@endsection