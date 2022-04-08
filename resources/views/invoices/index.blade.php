{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('invoices', request()->route('id')) }}
@endsection

{{-- Content --}}
@section('content')

<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-layers text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Trainer Invoices') }}</h3>
        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <div class="table-responsive">
            <table class="table table-separate table-head-custom table-checkable" id="trainer_invoice_datatable">
                <thead>
                    <tr>
                        <th class="id">ID</th>
                        <th>File</th>
                        <th>Invoice Id</th>
                        <th>Invoice Date</th>
                        <th>Final Amount</th>
                        <th width="10%">Amount</th>
                        <th>Amount Due</th>
                        <th>Status</th>
                        <th class="action">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!--end: Datatable-->
    </div>
</div>

<div class="card card-custom mt-6 pt-5">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-layers text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Customer Invoices') }}</h3>
        </div>
        <div class="card-toolbar">
        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <div class="table-responsive">
            <table class="table table-separate table-head-custom table-checkable" id="customer_invoice_datatable">
                <thead>
                    <tr>
                        <th class="id">ID</th>
                        <th>Title</th>
                        <th>Invoice Id</th>
                        <th>Invoice Date</th>
                        <th>Final Amount</th>
                        <th width="10%">Amount</th>
                        <th>Amount Due</th>
                        <th>Status</th>
                        <th class="action">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!--end: Datatable-->
    </div>
</div>
<!--end::Card-->
<!-- form Modal-->
@component('components.model')
@slot('title')
Invoice
@endslot
@slot('form')
{{ Form::open(['method' => 'POST', 'id' => 'invoice-form','class' => 'form', 'files' => true]) }}

{{ Form::close() }}
@endslot
@endcomponent
@endsection

@section('styles')
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/invoice.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
<script>
    var KTDatatablesSearchOptionsColumnSearch = function() {

        $.fn.dataTable.Api.register('column().title()', function() {
            return $(this.header()).text().trim();
        });

        var trainerInvoiceTable = function() {

            // begin first table
            var table = $('#trainer_invoice_datatable').DataTable({
                responsive: false,
                // Pagination settings
                dom: `<'row'<'col-sm-12'tr>>
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                // read more: https://datatables.net/examples/basic_init/dom.html

                lengthMenu: [5, 10, 25, 50],

                pageLength: 10,

                language: {
                    'lengthMenu': 'Display _MENU_',
                },

                searchDelay: 500,
                processing: true,
                serverSide: true,
                "order": false,
                ajax: {
                    url: "{{route('tickets.invoices.getlist', ['id' => request()->route('id')])}}",
                    type: 'GET',
                    data: function(data) {
                        $('.datatable-input').each(function(e) {
                            data[$(this).attr('name')] = this.value;
                        });

                        return data;
                    },
                },
                columnDefs: [{
                        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        orderable: false
                    },
                    {
                        targets: 7,
                        render: function(data, type, full, meta) {
                            var status = {
                                1: {
                                    'title': 'Paid',
                                    'class': ' label-light-success'
                                },
                                2: {
                                    'title': 'Due',
                                    'class': ' label-light-danger'
                                },
                            };
                            if (typeof status[data] === 'undefined') {
                                return data;
                            }
                            return '<span class="label label-lg font-weight-bold' + status[data].class + ' label-inline">' + status[data].title + '</span>';
                        },
                    },
                ],
                initComplete: function() {
                    var thisTable = this;
                    var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));
                },
            });
            table.on('order.dt search.dt', function() {
                table.column(0, {
                    search: 'applied',
                    order: false
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        };

        var customerInvoiceTable = function() {

            // begin first table
            var table = $('#customer_invoice_datatable').DataTable({
                responsive: false,
                // Pagination settings
                dom: `<'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                // read more: https://datatables.net/examples/basic_init/dom.html

                lengthMenu: [5, 10, 25, 50],

                pageLength: 10,

                language: {
                    'lengthMenu': 'Display _MENU_',
                },

                searchDelay: 500,
                processing: true,
                serverSide: true,
                "order": false,
                ajax: {
                    url: "{{route('tickets.invoices.getlist', ['id' => request()->route('id'), 'invoice_type' => 1])}}",
                    type: 'GET',
                    data: function(data) {
                        $('.datatable-input').each(function(e) {
                            data[$(this).attr('name')] = this.value;
                        });

                        return data;
                    },
                },
                initComplete: function() {
                    var thisTable = this;
                    var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));
                },
                columnDefs: [{
                        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        orderable: false
                    },
                    {
                        targets: 7,
                        render: function(data, type, full, meta) {
                            var status = {
                                1: {
                                    'title': 'Paid',
                                    'class': ' label-light-success'
                                },
                                2: {
                                    'title': 'Due',
                                    'class': ' label-light-danger'
                                },
                            };
                            if (typeof status[data] === 'undefined') {
                                return data;
                            }
                            return '<span class="label label-lg font-weight-bold' + status[data].class + ' label-inline">' + status[data].title + '</span>';
                        },
                    },
                ],
            });
            table.on('order.dt search.dt', function() {
                table.column(0, {
                    search: 'applied',
                    order: false
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        };

        return {
            //main function to initiate the module
            init: function() {
                trainerInvoiceTable();
                customerInvoiceTable();
            },

        };
    }();

    jQuery(document).ready(function() {
        KTDatatablesSearchOptionsColumnSearch.init();

        // @if(Session::has('errors'))
        //     $('#exampleModal').modal({show: true});
        // @endif

        $(document).on('click', '.invoice-modal', function(e) {
            $this = $(this);
            var form = $('.modal-content form#invoice-form');
            form.load($this.data('url'), function(response, status, xhr) {
                if (xhr.status == 200) {
                    form.attr('action', $this.data('url'));
                    $("#exampleModal").modal("show");
                } else {
                    toastr["error"]("Something went wrong, Please try again!");
                }
            });
        });
    });
</script>
@endsection