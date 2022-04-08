{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('view-invoices-title', $invoice) }}
@endsection

{{-- Content --}}
@section('content')
<div class="card card-custom overflow-hidden">
    <div class="card-header">
        @component('components.setActionBtn')
            @slot('title')
                View Invoice
            @endslot
            @slot('cancelRoute')
                {{ route('tickets.invoices', request()->route('id')) }}
            @endslot
        @endcomponent
    </div>
    <div class="card-body p-0">
        <div class="row justify-content-center bgi-size-cover bgi-no-repeat py-8 px-8 py-md-27 px-md-0" style="background-image: url({{ asset('media/bg/bg-6.jpg')}});">
            <div class="col-md-9">
                <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                    <h1 class="display-4 text-white font-weight-boldest mb-10">INVOICE</h1>
                    <div class="d-flex flex-column align-items-md-end px-0">
                        <h3 class="text-white font-weight-bolder mb-5">
                            {{ config('app.name') }}
                        </h3>
                        <!-- <span class="text-white d-flex flex-column align-items-md-end opacity-70">
                            <span>Cecilia Chapman, 711-2880 Nulla St, Mankato</span>
                            <span>Mississippi 96522</span>
                        </span> -->
                    </div>
                </div>
                <div class="border-bottom w-100 opacity-20"></div>
                <div class="d-flex justify-content-between text-white pt-6">
                    <div class="d-flex flex-column flex-root">
                        <span class="font-weight-bolder mb-2">INVOICE DATE</span>
                        <span class="opacity-70">{{ !empty($invoice->formated_invoice_date) ? $invoice->formated_invoice_date : config('constants.DEFAULT_MSG') }}</span>
                    </div>
                    <div class="d-flex flex-column flex-root">
                        <span class="font-weight-bolder mb-2">INVOICE NO.</span>
                        <span class="opacity-70">{{ $invoice->invoice_number ?? config('constants.DEFAULT_MSG') }}</span>
                    </div>
                    <div class="d-flex flex-column flex-root">
                        <span class="font-weight-bolder mb-2">DUE DATE</span>
                        <span class="opacity-70">{{ !empty($invoice->formated_due_date) ? $invoice->formated_due_date : config('constants.DEFAULT_MSG') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-9">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="pl-0 font-weight-bold text-muted text-uppercase">Description</th>
                                <!-- <th class="text-right font-weight-bold text-muted text-uppercase">Status</th> -->
                                <!-- <th class="text-right font-weight-bold text-muted text-uppercase">Rate</th> -->
                                <th class="text-right pr-0 font-weight-bold text-muted text-uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-weight-boldest font-size-lg">
                                <td class="pl-0 pt-7">{{ $invoice->name ?? config('constants.DEFAULT_MSG') }}</td>
                                <!-- <td class="text-right label label-lg label-inline {{CommonHelper::quoteStatusLabel($invoice->payment_status)['class']}}">{{CommonHelper::quoteStatusLabel($invoice->payment_status)['title'] ?? config('constants.DEFAULT_MSG')}}</td> -->
                                <!-- <td class="text-right pt-7">$40.00</td> -->
                                <td class="text-danger pr-0 pt-7 text-right">{!! $invoice->amount_label ?? config('constants.DEFAULT_MSG') !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-9">
                <div class="d-flex justify-content-between flex-column flex-md-row font-size-lg">
                    <!-- Invicible content start -->
                    <div class="d-flex flex-column mb-10 mb-md-0 invisible">
                        <div class="font-weight-bolder font-size-lg mb-3">BANK TRANSFER</div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="mr-15 font-weight-bold">Account Name:</span>
                            <span class="text-right">Barclays UK</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="mr-15 font-weight-bold">Account Number:</span>
                            <span class="text-right">1234567890934</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="mr-15 font-weight-bold">Code:</span>
                            <span class="text-right">BARC0032UK</span>
                        </div>
                    </div>
                    <!-- Invicible content end -->
                    <div class="d-flex flex-column text-md-right">
                        <span class="font-size-lg font-weight-bolder mb-1">TOTAL AMOUNT</span>
                        <span class="font-size-h2 font-weight-boldest text-danger mb-1">{!! $invoice->amount_label ?? config('constants.DEFAULT_MSG') !!}</span>
                        <!-- <span>Taxes Included</span> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0" id="invoice_footer">
            <div class="col-md-9">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-light-primary font-weight-bold invisible" onclick="window.print();">Print Invoice</button>
                    <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Download Invoice</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- Style Section --}}
@section('styles')
<style>
@media print {
    #kt_aside, #kt_header, #kt_subheader, #kt_footer, #kt_quick_user,
    #kt_quick_panel, #kt_header_mobile, #invoice_footer, .card-header {
        visibility: hidden;
    }
    .card-header {
        display: none;
    }
}
</style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script type="text/javascript">
    function printDiv(selector) {
        // var divContents = $('body .'+selector).html();
        // var a = window.open('', '', 'height=500, width=500'); 
        // a.document.write('<html>'); 
        // a.document.write('<body><br>'); 
        // a.document.write(divContents); 
        // a.document.write('</body></html>'); 
        // a.document.close(); 
        // a.print(); 

        var printContents = $('body .'+selector).html();
        var originalContents = $('body').html();
        $('body').html(printContents);
        window.print();
        // window.close();
        $('body').html(originalContents);
    }
    jQuery(document).ready(function() {
        window.onbeforeprint = function() {
            $('#kt_wrapper').removeClass('wrapper');
        }
        window.onafterprint = function() {
            $('#kt_wrapper').addClass('wrapper');
        }
    });
</script>
@endsection