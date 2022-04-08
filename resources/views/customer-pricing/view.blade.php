<div class="modal-body">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">Name:</label>
            <div class="col-lg-8">
                <span class="form-control-plaintext text-left">{{ $installment->name ?? config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">Amount:</label>
            <div class="col-lg-8">
                <span class="form-control-plaintext text-left">{!! $installment->amount_label ?? config('constants.DEFAULT_MSG') !!}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">Due Date:</label>
            <div class="col-lg-8">
                <span class="form-control-plaintext text-left">{{ $installment->formated_due_date ?? config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">Status:</label>
            <div class="col-lg-8">
                <span class="label label-lg text-left label-inline {{CommonHelper::quoteStatusLabel($installment->payment_status)['class']}}">
                    {{CommonHelper::quoteStatusLabel($installment->payment_status)['title'] ?? config('constants.DEFAULT_MSG')}}
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">Invoice Number:</label>
            <div class="col-lg-8">
                <span class="form-control-plaintext text-left">{{ $installment->invoice_number ?? config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-4 col-form-label font-weight-bolder text-left">Invoice Date:</label>
            <div class="col-lg-8">
                <span class="form-control-plaintext text-left">{{ !empty($installment->formated_invoice_date) ? $installment->formated_invoice_date : config('constants.DEFAULT_MSG') }}</span>
            </div>
        </div>
    </div>
</div>