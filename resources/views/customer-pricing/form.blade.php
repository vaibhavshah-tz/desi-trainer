<div class="card-body">
    <div class="form-group row">
        {{ Form::hidden('ticket_id', $ticketDetail->id ?? '', ['id' => 'ticket_id']) }}
        {{ Form::hidden('customer_id', $ticketDetail->customer->id ?? '', ['id' => 'customer_id']) }}
        <div class="col-lg-5">
            {{ Form::label('quote', 'Final Pricing <span style="color: red">*</span>','',false ) }}
            {{ Form::number('quote', $ticketDetail->customerQuote->quote ?? '', ['min' => '0','class' => 'form-control '.($errors->has('quote') ? 'is-invalid' : ''), 'placeholder' => 'Enter final pricing', 'id' => 'customer-quote']) }}
            @error('quote')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
        <div class="col-lg-5">
            {{ Form::label('currency', 'Currency <span style="color: red">*</span>','',false ) }}
            {{ Form::select('currency', CommonHelper::getCurrency(), $ticketDetail->customerQuote->currency ?? '', ['class' => 'form-control', 'placeholder' => 'Select currency', 'id' => 'customer-currency']) }}
            @error('currency')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
        <div class="col-lg-2 d-flex align-items-end">
            {{ Form::label('', '') }}
            {{ Form::submit('Save', ['class' => 'form-control btn btn-primary mr-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => 'Save']) }}
        </div>
    </div>
</div>
{{-- Style Section --}}
@section('styles')
<style>
    .dis-text{
        display: block;
        color: #f98787;
        font-weight: 500;
        position: absolute;
    }
</style>
@endsection

{{-- Scripts Section --}}
@push('scripts')
<script>
    /**
     * Calculate the discount on price
     */
    $(document).on('keyup', '#customer-quote', function() {
        var currency = $('#customer-currency').val();
        var finalPrice = parseFloat($('#customer-quote').val());
        if(currency != '') {
            $.ajax({
                type: 'POST',
                url: "{{ route('check-price-rate') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {ticket_id: $('#ticket_id').val(), currency: currency},
                success: function(data, status, xhr) {
                    if (xhr.status == 200 && data.status == 1) {
                        if ($.isNumeric(data.price) && finalPrice && data.price > finalPrice) {
                            var percentage = (((data.price - finalPrice) * 100) / data.price);
                            $("body .dis-text").remove();
                            $('<span class="dis-text">' + percentage.toFixed(2) + '% discount added. </span>').insertAfter("#customer-quote");
                        } else {
                            $("body .dis-text").remove();
                        }
                    } else {
                        $("body .dis-text").remove();
                    }
                },
                error: function(data) {
                    $("body .dis-text").remove();
                }
            });
        } else {
            $("body .dis-text").remove();
        }
    });
</script>
@endpush