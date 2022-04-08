{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('sub-admin-edit-title', $user) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        Edit sub admin
        @endslot
        @permission('view-sub-admin')
            @slot('viewRoute')
            {{ route('subadmin.view', $user->id) }}
            @endslot
        @endpermission
        @permission('delete-sub-admin')
            @slot('deleteRoute')
            {{ route('subadmin.delete', $user->id) }}
            @endslot
        @endpermission
        @permission('sub-admin-list')
            @slot('cancelRoute')
            {{ route('subadmin.index') }}
            @endslot
        @endpermission
        @endcomponent
    </div>
    {{ Form::model($user, ['route' => ['subadmin.edit', $user->id], 'method' => 'patch','id' => 'sub-admin-add','class' => 'form', 'enctype' => 'multipart/form-data']) }}
        @include('subAdmins._form')
        {{ Form::hidden('user_id', $user->id ?? 0, ['id' => 'user-id']) }}
    {{ Form::close() }}
</div>
<!--end::Card-->
@endsection