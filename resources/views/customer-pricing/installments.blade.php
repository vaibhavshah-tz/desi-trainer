<div class="card-header">
    <h3 class="card-title">{{ @trans('Installments') }}</h3>
    <div class="card-toolbar">
        <button href="javascript:void(0)" data-url="{{route('tickets.customer-pricing.installments.create', ['id' => $ticketDetail->id, 'customer_quote_id' => $ticketDetail->customerQuote->id])}}" class="btn btn-sm btn-primary font-weight-bolder installment-modal" data-toggle="tooltip" data-theme="dark" title="{{ __('Create Installment') }}">
            <i class="la la-plus"></i>{{ __('Create Installment') }}</button>
    </div>
</div>
<div class="card-body py-4">
    <div class="table-responsive">
        <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
            <thead>
                <tr class="text-uppercase">
                    <th style="min-width: 250px" class="pl-7">
                        <span class="text-dark-75">Name</span>
                    </th>
                    <th style="min-width: 150px">
                        <span class="text-dark-75">Amount</span>
                    </th>
                    <th style="min-width: 150px">
                        <span class="text-dark-75">Due Date</span>
                    </th>
                    <th style="min-width: 100px">
                        <span class="text-dark-75">Status</span>
                    </th>
                    <th style="min-width: 100px">
                        <span class="text-dark-75">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($ticketDetail->customerQuote->installments as $installment)
                <tr>
                    <td>
                        <span class="d-block font-size-lg">
                            {{ $installment->name ?? config('constants.DEFAULT_MSG') }}
                        </span>
                    </td>
                    <td>
                        <span class="d-block font-size-lg">
                            {!! $installment->amount_label ?? config('constants.DEFAULT_MSG') !!}
                        </span>
                    </td>
                    <td>
                        <span class="d-block font-size-lg">
                            {{ $installment->formated_due_date ?? config('constants.DEFAULT_MSG') }}
                        </span>
                    </td>
                    <td>
                        <span class="label label-lg label-inline {{CommonHelper::quoteStatusLabel($installment->payment_status)['class']}}">
                            {{CommonHelper::quoteStatusLabel($installment->payment_status)['title']}}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-icon btn-light btn-hover-primary btn-sm installment-modal" data-url="{{route('tickets.customer-pricing.installments.view', ['id' => $ticketDetail->id, 'customer_quote_id' => $ticketDetail->customerQuote->id, 'installment_id' => $installment->id])}}">
                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                {{ Metronic::getSVG("media/svg/icons/General/Visible.svg", "svg-icon") }}
                            </span>
                        </button>
                        @if($installment->payment_status != config('constants.PAYMENT.PAID'))
                        <button class="btn btn-icon btn-light btn-hover-primary btn-sm mx-3 installment-modal" data-url="{{route('tickets.customer-pricing.installments.edit', ['id' => $ticketDetail->id, 'customer_quote_id' => $ticketDetail->customerQuote->id, 'installment_id' => $installment->id])}}">
                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                {{ Metronic::getSVG("media/svg/icons/Communication/Write.svg", "svg-icon") }}
                            </span>
                        </button>
                        @if($loop->count != 1)
                        <a href="{{route('tickets.customer-pricing.installments.delete', ['id' => $ticketDetail->id, 'customer_quote_id' => $ticketDetail->customerQuote->id, 'installment_id' => $installment->id])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm delete-record">
                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                {{ Metronic::getSVG("media/svg/icons/General/Trash.svg", "svg-icon") }}
                            </span>
                        </a>
                        @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="text-center pr-0">
                    <td class="pl-0 py-8" colspan="5">{{ __('No Installmets found') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<!-- form Modal-->
@component('components.model')
@slot('title')
Installment
@endslot
@slot('form')
{{ Form::open(['method' => 'POST', 'route' => ['tickets.customer-pricing.installments.create', ['id' => $ticketDetail->id, 'customer_quote_id' => $ticketDetail->customerQuote->id]], 'id' => 'quote-installment-form','class' => 'form']) }}

{{ Form::close() }}
@endslot
@endcomponent

{{-- Scripts Section --}}
@push('scripts')
<script>
    function setDatePicker() {
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        $('body #date').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            startDate: today,
            autoclose: true
        });
    }

    @if(Session::has('errors'))
    $('#exampleModal').modal({
        show: true
    });
    @endif

    checkInstallmentAmount = "{{ route('tickets.customer-pricing.installments.check-amount', ['id' => $ticketDetail->id, 'customer_quote_id' => $ticketDetail->customerQuote->id]) }}";
    $(document).on('click', '.installment-modal', function(e) {
        $this = $(this);
        var form = $('.modal-content form#quote-installment-form');
        form.load($this.data('url'), function(response, status, xhr) {
            if (xhr.status == 200) {
                form.attr('action', $this.data('url'));
                setDatePicker();
                $("#exampleModal").modal("show");
            } else {
                toastr["error"]("Something went wrong, Please try again!");
            }
        });
    });

    $(document).on('click', '.delete-record', function(e) {
        e.preventDefault();
        $me = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "It will permanently deleted !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, delete it!",
        }).then(function(data) {
            if (data.isConfirmed) {
                window.location.href = $me.attr('href');
            }
        });
    });
</script>
@endpush