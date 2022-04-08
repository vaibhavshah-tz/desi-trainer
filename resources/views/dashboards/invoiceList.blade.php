<div class="col-lg-6">
    <!--begin::List Widget 3-->
    <div class="card card-custom card-stretch dashboard-invoice-div">
        <!--begin::Header-->
        <div class="card-header border-0">
            <h3 class="card-title font-weight-bolder text-dark">{{ __('Invoice') }}</h3>
            <div class="card-toolbar">
                <div class="dropdown dropdown-inline">
                    <!-- <a href="#" class="btn btn-light-primary btn-sm font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">August</a> -->
                    <!-- <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"> -->
                    {{ Form::select('invoiceStatus', CommonHelper::getPaymentStatus(), null, ['class' => 'form-control datatable-input', 'placeholder' => 'Filter ', 'id' => 'filterInvoiceStatus']) }}
                    <!-- </div> -->
                </div>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body pt-2">
            <!--begin: Datatable-->
            <div class="table-responsive">
                <table class="table table-separate table-head-custom table-checkable" id="invoice_datatable">
                    <thead>
                        <tr>
                            <th>{{ __('Invoice ID') }}</th>
                            <th>{{ __('Trainers') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!--end: Datatable-->
        </div>
        <!--end::Body-->
    </div>
    <!--end::List Widget 3-->
</div>