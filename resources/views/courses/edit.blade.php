{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('courses-edit-title',$courseDetails) }}
@endsection

{{-- Content --}}
@section('content')
<div class="card card-custom">
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        {{ __('Edit Course') }}
        @endslot
        @slot('viewRoute')
        {{ route('courses.view', $courseDetails->id) }}
        @endslot
        @slot('deleteRoute')
        {{ route('courses.delete', $courseDetails->id) }}
        @endslot
        @slot('cancelRoute')
        {{ route('courses.index') }}
        @endslot
        @endcomponent
    </div>

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
    <!--begin::Add New Card-->
    <div class="card-body kyc-form">
        <!--begin: Datatable-->
        <div class="kyc-form-inner d-flex">
            <ul id="tabs" class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a id="tab-A" href="#pane-A" class="nav-link active" data-toggle="tab" role="tab">Course Detail</a>
                </li>
                <li class="nav-item">
                    <a id="tab-B" href="#pane-B" class="nav-link" data-toggle="tab" role="tab">Course Features</a>
                </li>
                <li class="nav-item">
                    <a id="tab-C" href="#pane-C" class="nav-link section-title-list" data-toggle="tab" role="tab">Course Curriculum</a>
                </li>
                <li class="nav-item">
                    <a id="tab-D" href="#pane-d" class="nav-link" data-toggle="tab" role="tab">FAQ's</a>
                </li>
            </ul>
            <!--begin: Wizard Form-->
            <!-- {{ Form::open(['id' => 'kt_form','class' => 'form course-add', 'enctype' => 'multipart/form-data' ]) }} -->
            @include('courses._form')
            <!-- {{ Form::close() }} -->
            <!--end: Wizard Form-->
        </div>
        <!--end: Datatable-->
    </div>
    <!--end: Wizard-->

</div>
@endsection