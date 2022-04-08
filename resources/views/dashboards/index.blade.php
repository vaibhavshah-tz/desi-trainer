{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('home') }}
@endsection

{{-- Content --}}
@section('content')

{{-- Filters --}}
@include('dashboards.filters')

{{-- Counter with notifications --}}
<div class="row">
    @include('dashboards.counter')
    @include('dashboards.notification')
    @include('dashboards.courseList')
    @include('dashboards.invoiceList')
</div>
@endsection

{{-- Style Section --}}
@section('styles')
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    var url = window.location.origin;
    var oldSearchValue = {};
    var $adminSearch = "{{ $adminSearch }}";
    var $dateStringSearch = "{{ $dateStringSearch }}";
    var $startDate = "{{ $startDate }}";
    var $endDate = "{{ $endDate }}";

    ticketListing();
    courseListing();
    invoiceListing();

    /**
     * Set the searchable date into date picket
     */
    // if (dateSearch) {
    //     var dateSearch = new Date(dateSearch);
    //     var date = new Date(dateSearch);
    //     var day = ('0' + date.getDate()).slice(-2);
    //     var month = ('0' + (date.getMonth() + 1)).slice(-2);
    //     var year = date.getFullYear();

    //     $("#date").val(year + '-' + month + '-' + day);
    // }

    $('#kt_datepicker_5').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format: "yyyy-mm-dd",
        endDate: today,
        autoclose: true,
    });

    /**
     * Initialize the date picker
     */
    // $('#start_date, #end_date').datepicker({
    //     format: "yyyy-mm-dd",
    //     todayHighlight: true,
    //     endDate: today,
    //     autoclose: true,
    // });

    /**
     * Set the filter
     */
    $(document).on('change', '.filter', function() {
        var searchUrl = '';
        var searchName = $(this).attr('name');
        var searchValue = $(this).val();
        var createUrl = '';
        if (JSON.parse(sessionStorage.getItem("oldSearchValue")) !== null) {
            oldSearchValue = JSON.parse(sessionStorage.getItem("oldSearchValue"));
        }

        // Add search value into arrray and save in local session
        oldSearchValue[searchName] = searchValue;
        sessionStorage.setItem("oldSearchValue", JSON.stringify(oldSearchValue));
        // Set the array value in URL format
        for (var key in oldSearchValue) {
            if (!Date.parse(oldSearchValue['start_date'])) {
                delete oldSearchValue['end_date'];
            }
            if (oldSearchValue[key]) {
                createUrl += key + '=' + oldSearchValue[key] + '&';
            }
        }
        // Final URL for search
        searchUrl = (createUrl) ? url + '?' + createUrl.slice(0, -1) : url;
        console.log(searchUrl);
        window.location.replace(searchUrl);
    });

    /**
     * Ticket data table
     */
    function ticketListing() {
        var ticketColumnSearch = function() {
            $.fn.dataTable.Api.register('column().title()', function() {
                return $(this.header()).text().trim();
            });

            var ticketTable = function() {
                // begin first table
                var table = $('#ticket_datatable').DataTable({
                    responsive: false,
                    // Pagination settings
                    dom: `<'row'<'col-sm-12'tr>>
                            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // read more: https://datatables.net/examples/basic_init/dom.html

                    // lengthMenu: [5, 10, 25, 50],
                    pageLength: 5,
                    // language: {
                    //     'lengthMenu': 'Display _MENU_',
                    // },
                    "bLengthChange": false,
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "bInfo": false,
                    "order": [
                        [0, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('dashboard.getTicketlist') }}",
                        type: 'GET',
                        data: function(data) {
                            if ($adminSearch) {
                                data['sub_admin'] = $adminSearch;
                            }
                            if ($dateStringSearch) {
                                data['start_date'] = $dateStringSearch;
                            }
                            if ($startDate) {
                                data['start_date'] = $startDate;
                            }
                            if ($endDate) {
                                data['end_date'] = $endDate;
                            }

                            $('.datatable-input').each(function(e) {
                                if ($(this).attr('name') == 'ticketStatus') {
                                    data[$(this).attr('name')] = this.value;
                                }
                            });

                            return data;
                        },
                    },
                    columns: [{
                            data: 'created_at'
                        },
                        {
                            data: 'ticket_id'
                        },
                        {
                            data: 'ticket_type_name'
                        },
                        {
                            data: 'status'
                        }
                    ],
                    initComplete: function() {
                        var thisTable = this;
                        $('.datatable-input').unbind();
                        var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));

                    },
                    columnDefs: [{
                            targets: 0,
                            "visible": false,
                        },
                        {
                            targets: 1,
                            className: 'id-text',
                            render: function(data, type, full, meta) {
                                return '<a href="/tickets/view/' + full.id + '"  data-toggle="tooltip" data-theme="dark" title="View details">' + data + '</a>';
                            },
                            // "visible": false,
                        },
                        {
                            targets: 3,
                            render: function(data, type, full, meta) {
                                var status = {
                                    "{{ config('constants.TICKET.NEW') }}": {
                                        'title': "{{ config('constants.TICKET.NEW_LABEL') }}",
                                        'class': ' label-light-primary'
                                    },
                                    "{{ config('constants.TICKET.PENDING') }}": {
                                        'title': "{{ config('constants.TICKET.PENDING_LABEL') }}",
                                        'class': ' label-light-warning'
                                    },
                                    "{{ config('constants.TICKET.IN_PROGRESS') }}": {
                                        'title': "{{ config('constants.TICKET.IN_PROGRESS_LABEL') }}",
                                        'class': ' label-light-success'
                                    },
                                    "{{ config('constants.TICKET.INACTIVE') }}": {
                                        'title': "{{ config('constants.TICKET.INACTIVE_LABEL') }}",
                                        'class': ' label-light-dark'
                                    },
                                    "{{ config('constants.TICKET.COMPLETE') }}": {
                                        'title': "{{ config('constants.TICKET.COMPLETE_LABEL') }}",
                                        'class': ' label-light-info'
                                    },
                                    "{{ config('constants.TICKET.CANCEL') }}": {
                                        'title': "{{ config('constants.TICKET.CANCEL_LABEL') }}",
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
                $(document).on('change', '#filterTicketStatus', function(e) {
                    e.preventDefault();
                    table.table().draw();
                });
            };

            return {
                //main function to initiate the module
                init: function() {
                    ticketTable();
                },
            };
        }();

        jQuery(document).ready(function() {
            ticketColumnSearch.init();
        });
    }

    /**
     * Course data table
     */
    function courseListing() {
        var courseColumnSearch = function() {
            $.fn.dataTable.Api.register('column().title()', function() {
                return $(this.header()).text().trim();
            });

            var courseTable = function() {
                // begin first table
                var table = $('#course_datatable').DataTable({
                    responsive: false,
                    // Pagination settings
                    dom: `<'row'<'col-sm-12'tr>>
                    <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // read more: https://datatables.net/examples/basic_init/dom.html

                    // lengthMenu: [5, 10, 25, 50],
                    pageLength: 5,
                    // language: {
                    //     'lengthMenu': 'Display _MENU_',
                    // },
                    "bLengthChange": false,
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "bInfo": false,
                    "order": [
                        [0, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('dashboard.getCourseList') }}",
                        type: 'GET',
                        data: function(data) {
                            if ($adminSearch) {
                                data['sub_admin'] = $adminSearch;
                            }
                            if ($dateStringSearch) {
                                data['start_date'] = $dateStringSearch;
                            }
                            if ($startDate) {
                                data['start_date'] = $startDate;
                            }
                            if ($endDate) {
                                data['end_date'] = $endDate;
                            }

                            $('.datatable-input').each(function(e) {
                                if ($(this).attr('name') == 'courseStatus') {
                                    data[$(this).attr('name')] = this.value;
                                }
                            });

                            return data;
                        },
                    },
                    columns: [{
                            data: 'name'
                        },
                        {
                            data: 'course_price'
                        },
                        {
                            data: 'course_special_price'
                        },
                        {
                            data: 'status'
                        },
                    ],
                    initComplete: function() {
                        var thisTable = this;
                        $('.datatable-input').unbind();
                        var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));

                    },
                    columnDefs: [{
                            targets: 0,
                            render: function(data, type, full, meta) {
                                return (data != null || data != '') ? '<a href="/courses/view/' + full.id + '"  data-toggle="tooltip" data-theme="dark" title="View details">' + setCourseData(data) + '</a>' : '<span>--</span>';
                            },
                        },
                        {
                            targets: 3,
                            render: function(data, type, full, meta) {
                                var status = {
                                    "{{ config('constants.ACTIVE') }}": {
                                        'title': "{{ config('constants.ACTIVE_LABEL') }}",
                                        'class': ' label-light-primary'
                                    },
                                    "{{ config('constants.INACTIVE') }}": {
                                        'title': "{{ config('constants.INACTIVE_LABEL') }}",
                                        'class': 'label-light-danger'
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

                $(document).on('change', '#filterCourseStatus', function(e) {
                    e.preventDefault();
                    table.table().draw();
                });
                /** Set the course name lenght */
                function setCourseData(data) {
                    var wordMatch = '{{ config("constants.SHOW_SPECIFIC_WORD") }}';
                    if ((data && data !== 'null' && data !== 'undefined')) {
                        if (data.length > wordMatch) {
                            data = '<span data-toggle="tooltip" data-theme="dark" title="' + data + '">' + data.substring(0, wordMatch) + '...</span>';
                        }
                    }

                    return (data && data !== 'null' && data !== 'undefined') ? data : '<span>--</span>';
                }
            };

            return {
                //main function to initiate the module
                init: function() {
                    courseTable();
                },
            };
        }();

        jQuery(document).ready(function() {
            courseColumnSearch.init();
        });
    }

    /**
     * Invoice data table
     */
    function invoiceListing() {
        var invoiceColumnSearch = function() {
            $.fn.dataTable.Api.register('column().title()', function() {
                return $(this.header()).text().trim();
            });

            var invoiceTable = function() {
                // begin first table
                var table = $('#invoice_datatable').DataTable({
                    responsive: false,
                    // Pagination settings
                    dom: `<'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // read more: https://datatables.net/examples/basic_init/dom.html

                    // lengthMenu: [5, 10, 25, 50],
                    pageLength: 5,
                    // language: {
                    //     'lengthMenu': 'Display _MENU_',
                    // },
                    "bLengthChange": false,
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "bInfo": false,
                    "order": [
                        [0, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('dashboard.getInvoiceList') }}",
                        type: 'GET',
                        data: function(data) {
                            if ($adminSearch) {
                                data['sub_admin'] = $adminSearch;
                            }
                            if ($dateStringSearch) {
                                data['start_date'] = $dateStringSearch;
                            }
                            if ($startDate) {
                                data['start_date'] = $startDate;
                            }
                            if ($endDate) {
                                data['end_date'] = $endDate;
                            }

                            $('.datatable-input').each(function(e) {
                                if ($(this).attr('name') == 'invoiceStatus') {
                                    data[$(this).attr('name')] = this.value;
                                }
                            });

                            return data;
                        },
                    },
                    columns: [{
                            data: 'invoice_number'
                        },
                        {
                            data: 'trainer_full_name'
                        },
                        {
                            data: 'invoice_date'
                        },
                        {
                            data: 'amount'
                        },
                        {
                            data: 'payment_status'
                        },
                    ],
                    initComplete: function() {
                        var thisTable = this;
                        $('.datatable-input').unbind();
                        var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));

                    },
                    columnDefs: [{
                            targets: 0,
                            render: function(data, type, full, meta) {
                                return (data != null || data != '') ? '<a href="tickets/' + full.ticket_id + '/invoices"  data-toggle="tooltip" data-theme="dark" title="View details">' + data + '</a>' : '<span>--</span>';
                            },
                        },
                        {
                            targets: 2,
                            className: 'due-date',
                            render: function(data, type, full, meta) {
                                return (data) ? data : '<span>--</span>';
                            },
                        },
                        {
                            targets: 3,
                            render: function(data, type, full, meta) {
                                return (data) ? '<span class="label label-lg font-weight-bold label-light-info label-inline">' + full.currency + ' ' + data + '</span>' : '<span>--</span>';
                            },
                        },
                        {
                            targets: 4,
                            render: function(data, type, full, meta) {
                                var status = {
                                    "{{ config('constants.PAYMENT.PAID') }}": {
                                        'title': "{{ config('constants.PAYMENT.PAID_LABEL') }}",
                                        'class': ' label-light-success'
                                    },
                                    "{{ config('constants.PAYMENT.DUE') }}": {
                                        'title': "{{ config('constants.PAYMENT.DUE_LABEL') }}",
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

                $(document).on('change', '#filterInvoiceStatus', function(e) {
                    e.preventDefault();
                    table.table().draw();
                });
            };

            return {
                //main function to initiate the module
                init: function() {
                    invoiceTable();
                },
            };
        }();

        jQuery(document).ready(function() {
            invoiceColumnSearch.init();
        });
    }

    /**
     * Reset the date filter value
     */
    $(document).on('click', '#reset', function() {
        $(".filter").val(null);

        sessionStorage.removeItem("oldSearchValue");
        window.location.replace(url);
    });
</script>

@endsection