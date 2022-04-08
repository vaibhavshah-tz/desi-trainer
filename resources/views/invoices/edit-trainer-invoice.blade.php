@csrf
<div class="modal-body">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">{{ __('Invoice Number:') }}</label>
            <div class="col-lg-8">
                <span class="form-control-plaintext text-left">{{ $invoice->invoice_number ?? config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">{{ __('Invoice Date:') }}</label>
            <div class="col-lg-8">
                <span class="form-control-plaintext text-left">{{ !empty($invoice->formated_invoice_date) ? $invoice->formated_invoice_date : config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">{{ __('Amount:') }}</label>
            <div class="col-lg-8">
                <span class="form-control-plaintext text-left">{!! $invoice->amount_label ?? config('constants.DEFAULT_MSG') !!}</span>
            </div>
        </div>
        <div class="form-group row">
            {{ Form::label('payment_status', 'Status <span style="color: red">*</span>', ['class' => 'col-lg-4 col-form-label font-weight-bolder text-left'],false ) }}
            {{ Form::select('payment_status', CommonHelper::getPaymentStatus(), $invoice->payment_status ?? '', ['class' => 'form-control col-lg-8']) }}
            @error('payment_status')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="form-group row">
            {{ Form::label('file', __('File'), ['class' => 'col-lg-4 col-form-label font-weight-bolder text-left'],false ) }}
            <div class="col-lg-8">
                {{ Form::file('file', ['class' => 'form-control']) }}
                @error('file')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Save'), ['class' => 'btn btn-primary mr-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Save')]) }}
    {{ Form::button(__('Reset'), ['type' => 'reset', 'id' => 'reset','class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Reset')]) }}
</div>