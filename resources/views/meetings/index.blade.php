{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('meetings', request()->route('id')) }}
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
            <h3 class="card-label">{{ __('Scheduled Meetings') }}</h3>
        </div>
        <div class="card-toolbar">
            <!--begin::Button-->
            <a href="{{ route('tickets.meetings.create', request()->route('id')) }}" class="btn btn-primary font-weight-bolder" data-toggle="tooltip" data-theme="dark" title="{{ __('Create New Meeting') }}">
                <i class="la la-plus"></i>{{ __('Create New Meeting') }}</a>
            <!--end::Button-->
        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <div class="table-responsive">
            <table class="table table-separate table-head-custom table-checkable" id="schedule_meeting_datatable">
                <thead>
                    <tr>
                        <th class="id">ID</th>
                        <th>Admin Name</th>
                        <th>Customer Name</th>
                        <th>Trainer Name</th>
                        <th>Meeting Name</th>
                        <th>Date and Time</th>
                        <th>Timezone</th>
                        <th>URL</th>
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
            <h3 class="card-label">{{ __('History of Meetings') }}</h3>
        </div>
        <div class="card-toolbar">
        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <div class="table-responsive">
            <table class="table table-separate table-head-custom table-checkable" id="history_meeting_datatable">
                <thead>
                    <tr>
                        <th class="id">ID</th>
                        <th>Admin Name</th>
                        <th>Customer Name</th>
                        <th>Trainer Name</th>
                        <th>Meeting Name</th>
                        <th>Date and Time</th>
                        <th>Timezone</th>
                        <th>URL</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!--end: Datatable-->
    </div>
</div>
<!--end::Card-->
@endsection

@section('styles')
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/pages/widgets.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
<script>
    var KTDatatablesSearchOptionsColumnSearch = function() {

        $.fn.dataTable.Api.register('column().title()', function() {
            return $(this.header()).text().trim();
        });

        var scheduleMeetingTable = function() {

            // begin first table
            var table = $('#schedule_meeting_datatable').DataTable({
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
                    [0, 'desc']
                ],
                ajax: {
                    url: "{{route('tickets.meetings.getlist', ['id' => request()->route('id')])}}",
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
                    $('.datatable-input').unbind();
                    $(document).on('keyup change', '.datatable-input', function(e) {
                        var i = $(this).data('col-index');
                        var code = e.keyCode || e.which;
                        if (code == 13 || e.type === 'change') {
                            table.column(i).search(this.value).draw();
                        }
                    });
                    var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));
                    // this.api().columns().every(function() {
                    //     var column = this;
                    //     var input;
                    //     switch (column.title()) {
                    //         case 'Admin/Sub admin':
                    //             input = $(`<input type="text" name="admin_name" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //             break;
                    //         case 'Customer Name':
                    //             input = $(`<input type="text" name="customer_name" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //             break;
                    //         case 'Trainer Name':
                    //             input = $(`<input type="text" name="trainer_name" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //             break;
                    //         case 'Meeting Name':
                    //             input = $(`<input type="text" name="meeting_title" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //             break;
                    //         // case 'Date and Time of Meeting':
                    //         //     input = $(`<input type="text" name="meeting_date_time" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //         //     break;
                    //         case 'Timezone':
                    //             input = $(`<input type="text" name="timezone" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //             break;
                    //         case 'Meeting URL':
                    //             input = $(`<input type="text" name="meeting_url" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //             break;
                    //         case 'Actions':
                    //             var search = $(`
                    //             <button class="btn btn-primary kt-btn btn-sm kt-btn--icon" style="margin: 6%;" data-toggle="tooltip" data-theme="dark" title="Search">
                    // 		        <span>
                    // 		            <i class="la la-search pr-0"></i>							            
                    // 		        </span>
                    // 		    </button>`);
                    //             var reset = $(`
                    //             <button class="btn btn-secondary kt-btn btn-sm kt-btn--icon" style="margin: -2%;" data-toggle="tooltip" data-theme="dark" title="Reset">
                    // 		        <span>
                    // 		           <i class="la la-close pr-0"></i>							           
                    // 		        </span>
                    // 		    </button>`);

                    //             $('<th>').append(search).append(reset).appendTo(rowFilter);

                    //             $(search).on('click', function(e) {
                    //                 e.preventDefault();
                    //                 // var params = {};
                    //                 // $(rowFilter).find('.datatable-input').each(function() {
                    //                 //     var i = $(this).data('col-index');
                    //                 //     if (params[i]) {
                    //                 //         params[i] += '|' + $(this).val();
                    //                 //     } else {
                    //                 //         params[i] = $(this).val();
                    //                 //     }
                    //                 // });
                    //                 // $.each(params, function(i, val) {
                    //                 //     // apply search params to datatable
                    //                 //     table.column(i).search(val ? val : '', false, false);
                    //                 // });
                    //                 table.table().draw();
                    //             });

                    //             $(reset).on('click', function(e) {
                    //                 e.preventDefault();
                    //                 $(rowFilter).find('.datatable-input').each(function(i) {
                    //                     $(this).val('');
                    //                     table.column($(this).data('col-index')).search('', false, false);
                    //                 });
                    //                 table.table().draw();
                    //             });
                    //             break;
                    //     }
                    //     if (column.title() != 'Actions') {
                    //         $(input).appendTo($('<th>').appendTo(rowFilter));
                    //     }
                    // });

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

                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, ],
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    var oSettings = this.fnSettings();
                    $("td:first", nRow).html(oSettings._iDisplayStart + iDisplayIndex + 1);
                    return nRow;
                },
            });
            // table.on('order.dt search.dt', function() {
            //     table.column(0, {
            //         search: 'applied',
            //         order: false
            //     }).nodes().each(function(cell, i) {
            //         cell.innerHTML = i + 1;
            //     });
            // }).draw();

            $(document).on('click', '.cancel-meeting', function(e) {
                e.preventDefault();
                $me = $(this);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "It will be canceled!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Yes!",
                }).then(function(data) {
                    if (data.isConfirmed) {
                        window.location.href = $me.attr('href');
                    }
                });
            });
        };

        var historyMeetingTable = function() {

            // begin first table
            var table = $('#history_meeting_datatable').DataTable({
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
                    [0, 'desc']
                ],
                ajax: {
                    url: "{{route('tickets.meetings.getlist', ['id' => request()->route('id'), 'history' => '1'])}}",
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
                    $('.datatable-input').unbind();
                    $(document).on('keyup change', '.datatable-input', function(e) {
                        var i = $(this).data('col-index');
                        var code = e.keyCode || e.which;
                        if (code == 13 || e.type === 'change') {
                            table.column(i).search(this.value).draw();
                        }
                    });
                    var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));
                    // this.api().columns().every(function() {
                    //     var column = this;
                    //     var input;
                    //     switch (column.title()) {
                    // case 'Admin/Sub admin':
                    //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //     break;
                    // case 'Customer Name':
                    //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //     break;
                    // case 'Trainer Name':
                    //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //     break;
                    // case 'Meeting Name':
                    //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //     break;
                    // case 'Date and Time of Meeting':
                    //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //     break;
                    // case 'Timezone':
                    //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //     break;
                    // case 'Meeting URL':
                    //     input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                    //     break;
                    // }
                    // if (column.title() != 'Actions') {
                    //     $(input).appendTo($('<th>').appendTo(rowFilter));
                    // }
                    // });

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

                },
                columnDefs: [{
                        targets: [0],
                        orderable: false
                    },
                    {
                        targets: 8,
                        render: function(data, type, full, meta) {
                            var status = {
                                1: {
                                    'title': 'Finished',
                                    'class': ' label-light-success'
                                },
                                2: {
                                    'title': 'Canceled',
                                    'class': ' label-light-danger'
                                },
                                0: {
                                    'title': 'Inactive',
                                    'class': ' label-light-warning'
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
                scheduleMeetingTable();
                historyMeetingTable();
            },

        };
    }();

    jQuery(document).ready(function() {
        KTDatatablesSearchOptionsColumnSearch.init();
    });
</script>
@endsection