{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('proposal-view-title', $proposal) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Profile Card-->
<div class="card card-custom card-stretch">
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        View Proposal
        @endslot
        @slot('editRoute')
        {{ route('tickets.proposals.edit', ['id' => $proposal->ticket_id, 'proposal_id' => $proposal->id]) }}
        @endslot
        @slot('deleteRoute')
        {{ route('tickets.proposals.delete', ['id' => $proposal->ticket_id, 'proposal_id' => $proposal->id]) }}
        @endslot
        @slot('cancelRoute')
        {{ route('proposals', $proposal->ticket_id) }}
        @endslot
        @endcomponent
    </div>
    <div class="card-body py-4">
        <div class="row view-detail">
            <div class="col-lg-6">
                <div class="form-group my-0 praposal-div">
                    <label class="col-form-label font-weight-bolder praposal-title">{{ __('Name:') }}</label>
                    <div class="praposal-text">
                        <span class="form-control-plaintext">{{ $proposal->name ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group my-0 praposal-div">
                    <label class="col-form-label font-weight-bolder praposal-title">{{ __('Quote:') }}</label>
                    <div class="praposal-text">
                        <span class="form-control-plaintext">{!! $proposal->quote_label ?? config('constants.DEFAULT_MSG') !!}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row view-detail">
            <div class="col-lg-12">
                <div class="form-group my-0 praposal-div">
                    <label class="col-form-label font-weight-bolder praposal-title">{{ __('Description:') }}</label>
                    <div class="praposal-text">
                        <span class="form-control-plaintext">{{ $proposal->description ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <h5 class="text-dark font-weight-bold">{{ __('Trainers:') }}</h5>
        <div class="separator separator-dashed my-8"></div>
        <div class="form-group row mb-6">
            <div class="col-lg-4 mb-lg-0 mb-6">
                {{ Form::select('trainer_status', CommonHelper::getProposalStatus(), null, ['class' => 'form-control search-input', 'placeholder' => 'Select status', 'id' => 'trainer-status']) }}
            </div>
            <div class="col-lg-4 mb-lg-0 mb-6">
                <button class="btn btn-primary kt-btn kt-btn--icon"  id="search">
                    <span>
                        <span>Search</span>
                    </span>
                </button>
                <button class="btn btn-secondary kt-btn kt-btn--icon" id="reset">
                    <span>
                        <span>Reset</span>
                    </span>
                </button>
            </div>
            <div class="col-lg-2 mb-lg-0 mb-6"></div>
            <div class="col-lg-2 mb-lg-0 mb-6"></div>
        </div>
        <!--begin::Table-->
        <div class="trainer-container">
            @include('proposals.view-trainer-list', $proposalTrainers)
        </div>
        <!--end::Table-->
    </div>
    <!--end::Body-->
</div>
<!--end::Profile Card-->

@endsection

{{-- Style Section --}}
@section('styles')
<style>
    .pagination {
        margin: 0px !important;
    }
</style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script>
    $(document).on('click', '.assign-trainer', function(e) {
        e.preventDefault();
        $me = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: $me.data('name') + " will be assigned to this ticket!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, assign!",
        }).then(function(data) {
            if (data.isConfirmed) {
                window.location.href = $me.data('link');
            }
        });
    });

    $(document).on('click', '#search', function(e) {
        var trainerStatus = $('[name=trainer_status]').val();
        $('.trainer-container').load("/tickets/{{$proposal->ticket_id}}/proposals/view/{{$proposal->id}}?trainer_status="+trainerStatus, function(response, status, xhr) {
            loadCallJs();
            if (xhr.status != 200) {
                toastr["error"]("Something went wrong, Please try again!");
            }
        });
    });
    $(document).on('click', '#reset', function(e) {
        $('.search-input').val('');
        $('#search').trigger('click');
    });
</script>
@endsection