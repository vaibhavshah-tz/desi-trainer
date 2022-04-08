{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('customer-pricing', $ticketDetail->id) }}
@endsection

{{-- Content --}}
@section('content')

<div class="card card-custom">
    @if($ticketDetail->ticket_type_id == config('constants.TICKET_TYPE.TRAINING_KEY'))
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        Course Details
        @endslot
        @endcomponent
    </div>
    <div class="card-body view-detail">
        <div class="form-group row">
            <label class="col-lg-2 col-form-label font-weight-bolder text-left">Course Price:</label>
            <div class="col-lg-3">
                <span class="form-control-plaintext text-left">{!! $ticketDetail->course->price_with_currency_label ?? config('constants.DEFAULT_MSG') !!}</span>
            </div>
            <label class="col-lg-2 col-form-label font-weight-bolder text-left">Special Price:</label>
            <div class="col-lg-3">
                <span class="form-control-plaintext text-left" data-price="{{$ticketDetail->course->course_special_price ?? ''}}" id="special-price">{!! $ticketDetail->course->special_price_with_currency_label ?? config('constants.DEFAULT_MSG') !!}</span>
            </div>
        </div>
    </div>
    @endif
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        Customer Pricing
        @endslot
        @endcomponent
    </div>
    @if($ticketDetail->has_paid_installment == 0)
    {{ Form::open(['class' => 'form', 'id' => 'customer-pricing-form']) }}
    @include('customer-pricing.form')
    {{ Form::close() }}
    @else
    <div class="card-body view-detail">
        <div class="form-group row">
            <label class="col-lg-2 col-form-label font-weight-bolder text-left">Final pricing:</label>
            <div class="col-lg-3">
                <span class="form-control-plaintext text-left">{!! $ticketDetail->customerQuote->quote_label ?? config('constants.DEFAULT_MSG') !!}</span>
            </div>
        </div>
    </div>
    @endif

    @if($ticketDetail->customerQuote)
    @include('customer-pricing.installments')
    @endif
</div>
<!--end::Card-->

@endsection

@section('styles')
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script>
    var checkInstallmentAmount = '';
    var checkQuoteAmount = "{{ route('tickets.customer-pricing.check-amount', ['id' => $ticketDetail->id]) }}";
    var errorMsg = "{{ ($ticketDetail->ticket_type_id == config('constants.TICKET_TYPE.TRAINING_KEY')) ? 'Price must be less than course special price' : 'Price must be match minimum pricing criteria' }}";
</script>
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/customer-pricing.js') }}" type="text/javascript"></script>
@endsection