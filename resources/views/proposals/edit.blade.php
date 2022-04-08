{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('proposal-edit-title', $proposal) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        @component('components.setActionBtn')
            @slot('title')
                Edit Proposal
            @endslot
            @slot('viewRoute')
                {{ route('tickets.proposals.view', ['id' => $proposal->ticket_id, 'proposal_id' => $proposal->id]) }}
            @endslot
            @slot('deleteRoute')
                {{ route('tickets.proposals.delete', ['id' => $proposal->ticket_id, 'proposal_id' => $proposal->id]) }}
            @endslot
            @slot('cancelRoute')
                {{ route('proposals', $proposal->ticket_id) }}
            @endslot
        @endcomponent
    </div>
    {{ Form::model($proposal, ['id' => 'proposal-form','class' => 'form']) }}
    @include('proposals._form')
    {{ Form::close() }}
</div>
<!--end::Card-->
@endsection