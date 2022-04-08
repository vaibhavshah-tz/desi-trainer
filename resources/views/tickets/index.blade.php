{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('tickets') }}
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
            <h3 class="card-label">{{ __('Ticket Listing') }}</h3>
        </div>
        <div class="card-toolbar">
        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <div class="table-responsive">
            <table class="table table-separate table-head-custom table-checkable" id="ticket_datatable">
                <thead>
                    <tr>
                        <!-- <th class="id">ID</th> -->
                        <th>ID</th>
                        <th>Type</th>
                        <th>Course</th>
                        <th>Admin/Sub admin</th>
                        <th>Customer</th>
                        <th>Trainer</th>
                        <th>Created At</th>
                        <th>Requested For</th>
                        <th>Proposals</br><span class="label label-sm label-warning mr-2"></span>Accepted</br><span class="label label-sm label-primary mr-2"></span>Interested</th>
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
Assign Admin
@endslot
@slot('form')
{{ Form::open(['method' => 'POST', 'route' => 'tickets.assign-admin', 'id' => 'assign-admin-form','class' => 'form']) }}

{{ Form::close() }}
@endslot
@endcomponent

@endsection
@section('styles')
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/pages/widgets.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/ticket.js') }}" type="text/javascript"></script>
<script>
    var ticketType = JSON.parse(@json($ticketType));
    var userType = "{{ (request()->filled('type')) ? request()->type : '' }}";
    var userId = "{{ (request()->filled('user')) ? request()->user : '' }}";
    var ticketDuration = "{{ config('constants.CRON_TIME.TICKET_MINUTE') }}";

    var KTDatatablesSearchOptionsColumnSearch = function() {

        $.fn.dataTable.Api.register('column().title()', function() {
            return $(this.header()).text().trim();
        });

        var initTable1 = function() {

            // begin first table
            var table = $('#ticket_datatable').DataTable({
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
                "order": [
                    [6, 'desc']
                ],
                ajax: {
                    url: `/tickets/get-list?type=${userType}&user=${userId}`,
                    type: 'GET',
                    data: {
                        // pass data
                    },
                    data: '_token = <?php echo csrf_token() ?>',
                },
                columns: [
                    // {
                    //     data: 'id'
                    // },
                    {
                        data: 'ticket_id'
                    },
                    {
                        data: 'ticket_type_name'
                    },
                    {
                        data: 'course_name'
                    },
                    {
                        data: 'admin_name'
                    },
                    {
                        data: 'customer_name'
                    },
                    {
                        data: 'trainer_name'
                    },
                    {
                        data: 'formated_created_at'
                    },
                    {
                        data: 'ticket_date_time'
                    },
                    {
                        data: 'interested_trainers_count'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: null,
                        responsivePriority: -1
                    },
                ],
                initComplete: function() {
                    var thisTable = this;
                    $('.datatable-input').unbind();
                    $(document).on('keyup change', '.datatable-input', function(e) {
                        var i = $(this).data('col-index');
                        var code = e.keyCode || e.which;
                        if (code == 13 || e.type === 'change') {
                            table.column(i).search(this.value).draw();
                        }
                    });
                    var rowFilter = $('<tr class="filter filter-row"></tr>').appendTo($(table.table().header()));
                    this.api().columns().every(function() {
                        var column = this;
                        var input;
                        switch (column.title()) {
                            case 'Type':
                                input = $(`<select class="datatable-input form-control form-control-sm form-filter" title="Select All" id="multiselect_filter" data-col-index="` + column.index() + `">
										<option value="">Select All</option></select>`);
                                $.each(ticketType, function(i, type) {
                                    $(input).append('<option value="' + i + '">' + type + '</option>');
                                });
                                break;
                            case 'ID':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Course':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Admin/Sub admin':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Customer':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Trainer':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                                // case 'Created At':
                                //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input datepicket-input" data-col-index="` + column.index() + `" readonly="readonly" placeholder="Select date"/>`);
                                //     break;
                                // case 'Requested Date/Time':
                                //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input datepicker-input" data-col-index="` + column.index() + `" readonly="readonly" placeholder="Select date"/>`);
                                //     break;

                            case 'Status':
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
                                input = $(`<select class="form-control form-control-sm form-filter datatable-input ticket-status" title="Select All" data-col-index="` + column.index() + `">
										<option value="">Select All</option></select>`);
                                $.each(status, function(i, item) {
                                    $(input).append('<option value="' + i + '">' + status[i].title + '</option>');
                                });
                                break;
                            case 'Actions':
                                var search = $(`
                                <button class="btn btn-primary kt-btn btn-sm kt-btn--icon search-action" style="margin: 6%;" data-toggle="tooltip" data-theme="dark" title="Search">
							       
							            <i class="la la-search pr-0"></i>							            
							      
							    </button>`);
                                var reset = $(`
                                <button class="btn btn-secondary kt-btn btn-sm kt-btn--icon" style="margin: -2%;" data-toggle="tooltip" data-theme="dark" title="Reset">
							        <span>
							           <i class="la la-close pr-0"></i>							           
							        </span>
							    </button>`);

                                $('<th>').append(search).append(reset).appendTo(rowFilter);

                                $(search).on('click', function(e) {
                                    e.preventDefault();
                                    var params = {};
                                    $(rowFilter).find('.datatable-input').each(function() {
                                        var i = $(this).data('col-index');
                                        if (params[i]) {
                                            params[i] += '|' + $(this).val();
                                        } else {
                                            params[i] = $(this).val();
                                        }
                                    });
                                    $.each(params, function(i, val) {
                                        // apply search params to datatable
                                        table.column(i).search(val ? val : '', false, false);
                                    });
                                    table.table().draw();
                                });

                                $(reset).on('click', function(e) {
                                    e.preventDefault();
                                    $(rowFilter).find('.datatable-input').each(function(i) {
                                        $(this).val('');
                                        table.column($(this).data('col-index')).search('', false, false);
                                    });
                                    table.table().draw();
                                });
                                break;
                        }
                        if (column.title() != 'Actions') {
                            $(input).appendTo($('<th>').appendTo(rowFilter));
                        }
                    });

                    // hide search column for responsive table
                    // var hideSearchColumnResponsive = function() {
                    //     thisTable.api().columns().every(function() {
                    //         var column = this
                    //         if (column.responsiveHidden()) {
                    //             $(rowFilter).find('th').eq(column.index()).show();
                    //         } else {
                    //             $(rowFilter).find('th').eq(column.index()).hide();
                    //         }
                    //     })
                    // };

                    // init on datatable load
                    //hideSearchColumnResponsive();
                    // recheck on window resize
                    //window.onresize = hideSearchColumnResponsive;

                    $('.datepicker-input').datepicker({
                        format: 'yyyy-mm-dd',
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        }
                    });
                },
                columnDefs: [{
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var assign = '';
                            if (data.user_id == null) {
                                assign = '<button class="btn btn-sm btn-primary assign-admin mr-2" data-id="' + data.id + '" data-toggle="tooltip" data-theme="dark" title="Assign ticket">Assign</button>';
                            } else if ("{{ \Auth::user()->role_id }}" == "{{ config('constants.ADMIN_ROLE.SUPER_ADMIN') }}") {
                                assign = '<button class="btn btn-sm btn-primary assign-admin mr-2" data-id="' + data.id + '" data-toggle="tooltip" data-theme="dark" title="Assign ticket">Assign</button>';
                            } else {
                                assign = '';
                            }
                            var globalText = (data.is_global == "{{ config('constants.TICKET.IS_GLOBAL_YES') }}") ? 'Remove Global' : 'Set Global';
                            // $assign = ;
                            return assign + '<button class="btn btn-sm btn-primary isGlobal remove-global-btn" data-id="' + data.id + '" data-is-global="' + data.is_global + '" data-toggle="tooltip" data-theme="dark" title="' + globalText + '">' + globalText + '</button>\
                            \<a href="/tickets/view/' + data.id + '" class="btn btn-sm btn-clean btn-icon view-icon" data-toggle="tooltip" data-theme="dark" title="View details">\
                                \<i class="la la-eye"></i>\
                            \</a>\
                            \<a href="/tickets/delete/' + data.id + '" class="btn btn-sm btn-clean btn-icon delete-record" data-toggle="tooltip" data-theme="dark" title="Delete">\
                                \<i class="la la-trash"></i>\
                            \</a>\
                            ';
                        },
                    },
                    {
                        targets: [6, 7, 8],
                        orderable: false
                    },
                    {
                        targets: 0,
                        className: 'id-text',
                        render: function(data, type, full, meta) {
                            return '<a href="/tickets/view/' + full.id + '"  data-toggle="tooltip" data-theme="dark" title="View details">' + data + '</a>';
                        }
                    },
                    {
                        targets: 1,
                        className: 'type-div',
                        render: function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        targets: 2,
                        className: 'course-div',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            return (data && data !== 'null' && data !== 'undefined') ? setCourseData(data) : setCourseData(full.other_course);
                        },
                    },
                    {
                        targets: 3,
                        render: function(data, type, full, meta) {
                            return (data != 'null' && data != null && data != '') ? modifyName(data) : '<span>--</span>';
                        },
                    },
                    {
                        targets: 4,
                        render: function(data, type, full, meta) {
                            return (data != 'null' && data != null && data != '') ? modifyName(data) : '<span>--</span>';
                        },
                    },
                    {
                        targets: 5,
                        render: function(data, type, full, meta) {
                            return (data != 'null' && data != null && data != '') ? modifyName(data) : '<span>--</span>';
                        },
                    },
                    {
                        targets: 6,
                        className: 'create-div',
                        render: function(data, type, full, meta) {
                            return data;
                        },
                    },
                    {
                        targets: 7,
                        render: function(data, type, full, meta) {
                            if (data == null || data == '') {
                                return '<span>--</span>';
                            }
                            return data;
                        },
                    },
                    {
                        targets: 8,
                        render: function(data, type, full, meta) {
                            var acceptedCount = 0;
                            full.proposals.forEach(arg => {
                                acceptedCount += arg.trainers_count;
                            });
                            return '<span class="label label-lg label-warning mr-2">' + acceptedCount + '</span> <span class="label label-lg label-primary mr-2">' + full.interested_trainers_count + '</span>';
                        },
                    },
                    {
                        targets: 9,
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
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    if (aData.duration < ticketDuration && aData.user_id == null && aData.trainer_id == null) {
                        $('td', nRow).css('background-color', '#ffdab5'); // orange                        
                    } else if (aData.user_id == null || aData.trainer_id == null) {
                        $('td', nRow).css('background-color', '#ffbdbd'); // red
                    } else {
                        $('td', nRow).css('background-color', '#c3eea3'); // green
                    }
                    // red  #ffbdbd duration
                    // orange #ffdab5
                    // green #c3eea3;
                },
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

            /** Modify the name lenght */
            function modifyName(data) {
                var splitStr = '';
                if (data != 'null' && data != null && data != '') {
                    var splitStr = data.split(' ');
                    splitStr = splitStr[0].charAt(0).toUpperCase() + splitStr[0].substring(1) + ' ' + splitStr[1].charAt(0).toUpperCase();
                }

                return splitStr;
            }
            // table.on('order.dt search.dt', function() {
            //     table.column(0, {
            //         search: 'applied',
            //         order: false
            //     }).nodes().each(function(cell, i) {
            //         cell.innerHTML = i + 1;
            //     });
            // }).draw();
            /** Delete the ticket record */
            $(document).on('click', '.delete-record', function(e) {
                e.preventDefault();
                $me = $(this);
                $.ajax({
                    type: 'POST',
                    url: $me.attr('href'),
                    dataType: 'json',
                    data: {
                        'check_allowance': 1
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data > 0) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'Admin has associated with ticket, so first close ticket and after delete the ticket.',
                                icon: "warning",
                                showCancelButton: true,
                                // confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                            });
                        } else {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'It will permanently deleted !',
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: "Yes, delete it!",
                            }).then(function(data) {
                                if (data.isConfirmed) {
                                    $.ajax({
                                        type: 'POST',
                                        url: $me.attr('href'),
                                        headers: {
                                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                        },
                                        success: function(data, status, xhr) {
                                            if (xhr.status == 200 && data.status == 1) {
                                                var params = {};
                                                $('tr.filter').find('.datatable-input').each(function() {
                                                    var i = $(this).data('col-index');
                                                    if (params[i]) {
                                                        params[i] += '|' + $(this).val();
                                                    } else {
                                                        params[i] = $(this).val();
                                                    }
                                                });
                                                $.each(params, function(i, val) {
                                                    // apply search params to datatable
                                                    table.column(i).search(val ? val : '', false, false);
                                                });
                                                table.table().draw();
                                                toastr.success(data.message);
                                            } else {
                                                toastr.error(data.message);
                                            }

                                        },
                                        error: function(data) {
                                            toastr.error("Can not delete record, Please try again.");
                                        }
                                    });
                                }
                            });
                        }
                    },
                    error: function(data, status, xhr) {
                        if (data.status == 403) {
                            toastr.error("Access Denied.");
                        } else {
                            toastr.error("Something went wrong, Please try again!");
                        }
                    }
                });
            });
            /**Update the is global records */
            $(document).on('click', '.isGlobal', function(e) {
                e.preventDefault();
                $me = $(this);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('tickets.update.isglobal') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        is_global: $me.attr('data-is-global'),
                        id: $me.attr('data-id'),
                    },
                    success: function(data) {
                        toastr.success("Ticket global status is updated.");
                        var isGlobal = ($me.attr('data-is-global') == "{{ config('constants.TICKET.IS_GLOBAL_YES') }}") ? "{{ config('constants.TICKET.IS_GLOBAL_NO') }}" : "{{ config('constants.TICKET.IS_GLOBAL_YES') }}";
                        var globalText = (isGlobal == "{{ config('constants.TICKET.IS_GLOBAL_YES') }}") ? 'Remove Global' : 'Set Global';
                        $me.attr("data-is-global", isGlobal);
                        $me.attr("title", globalText);
                        $me.text(globalText);
                    },
                    error: function(data) {
                        toastr.error("Ticket is not mark as global.");
                    }
                });
            });
        };

        return {

            //main function to initiate the module
            init: function() {
                initTable1();
            },

        };

    }();

    jQuery(document).ready(function() {
        KTDatatablesSearchOptionsColumnSearch.init();

        $(document).on('click', '.assign-admin', function(e) {
            $('.modal-content form').load('/tickets/get-assigned-admin/' + $(this).data('id'), function(response, status, xhr) {
                if (xhr.status == 200) {
                    $("#exampleModal").modal("show");
                } else {
                    toastr["error"]("Something went wrong, Please try again!");
                }
            })
        });

        $(document).on('change', '.modal-content #assign_to_self', function(event) {
            if ($(this).is(':checked')) {
                $('.modal-content #sub-admin-dropdown').prop('disabled', true);
            } else {
                $('.modal-content #sub-admin-dropdown').prop('disabled', false);
            }
        });

        $(document).on('change', '.modal-content #sub-admin-dropdown', function(event) {
            if ($(this).val() != '') {
                $('.modal-content #assign_to_self').prop('disabled', true);
            } else {
                $('.modal-content #assign_to_self').prop('disabled', false);
            }
        });

        /** Reset the value */
        $(document).on("click", "#reset", function() {
            $('.modal-content #sub-admin-dropdown').val('').trigger('change');
            $('.modal-content #assign_to_self').prop('checked', false).trigger('change');
        });
    });
</script>
@endsection