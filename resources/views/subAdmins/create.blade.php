{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('sub-admin-create') }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        <h3 class="card-title">{{ __('Create new sub admin') }}</h3>
        <div class="card-toolbar">
            @permission('sub-admin-list')
            <a href="{{ route('subadmin.index') }}" class="btn btn-light-success mr-2 btn-sm" data-toggle="tooltip" data-theme="dark" title="Back"><i class="flaticon-reply"></i>Back</a>
            @endpermission
        </div>
    </div>
    {{ Form::open(['id' => 'sub-admin-add','class' => 'form', 'enctype' => 'multipart/form-data' ]) }}
        @include('subAdmins._form')
    {{ Form::close() }}
</div>
<!--end::Card-->
@endsection