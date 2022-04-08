{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('sub-admin') }}
@endsection

{{-- Content --}}
@section('content')

{{-- Dashboard 1 --}}
<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-layers text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Sub Admin Listing') }}</h3>
        </div>
        <div class="card-toolbar">
            @permission('create-sub-admin')
            <!--begin::Button-->
            <a href="{{ route('subadmin.create') }}" class="btn btn-primary font-weight-bolder" data-toggle="tooltip" data-theme="dark" title="Add sub admin">
                <i class="la la-plus"></i>Add sub admin</a>
            <!--end::Button-->
            @endpermission
        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <table class="table table-separate table-head-custom table-checkable" id="subadmin_datatble">
            <thead>
                <tr>
                    <th class="id">ID</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th class="oncall-suprt">On-call Support</th>
                    <th>Status</th>
                    <th class="action">Actions</th>
                </tr>
            </thead>
        </table>
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

        var initTable1 = function() {

            // begin first table
            var table = $('#subadmin_datatble').DataTable({
                responsive: true,
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
                    url: '/sub-admins/get-list',
                    type: 'GET',
                    data: {
                        // parameters for custom backend script demo
                        // columnsDef: [
                        //     'id', 'first_name', 'last_name', 'email', 'status', 'Actions',
                        // ],
                    },
                    data: '_token = <?php echo csrf_token() ?>',
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'first_name'
                    },
                    {
                        data: 'last_name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'call_support'
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
                    var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));
                    this.api().columns().every(function() {
                        var column = this;
                        var input;
                        switch (column.title()) {
                            case 'First name':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Last name':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Email':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'On-call Support':
                                var oncallsupport = {
                                    1: {
                                        'title': 'Yes',
                                        'class': ' label-light-primary'
                                    },
                                    0: {
                                        'title': 'No',
                                        'class': 'label-light-danger'
                                    },
                                };
                                input = $(`<select class="form-control form-control-sm form-filter datatable-input" title="Select All" data-col-index="` + column.index() + `">
										<option value="">Select All</option></select>`);
                                $.each(oncallsupport, function(i, item) {
                                    $(input).append('<option value="' + i + '">' + oncallsupport[i].title + '</option>');
                                });
                                // column.data().unique().sort().each(function(d, j) {
                                //     $(input).append('<option value="' + d + '">' + status[d].title + '</option>');
                                // });
                                break;
                            case 'Status':
                                var status = {
                                    1: {
                                        'title': 'Active',
                                        'class': ' label-light-primary'
                                    },
                                    0: {
                                        'title': 'Inactive',
                                        'class': 'label-light-danger'
                                    },
                                };
                                input = $(`<select class="form-control form-control-sm form-filter datatable-input" title="Select All" data-col-index="` + column.index() + `">
										<option value="">Select All</option></select>`);
                                $.each(status, function(i, item) {
                                    $(input).append('<option value="' + i + '">' + status[i].title + '</option>');
                                });
                                // column.data().unique().sort().each(function(d, j) {
                                //     $(input).append('<option value="' + d + '">' + status[d].title + '</option>');
                                // });
                                break;
                            case 'Actions':
                                var search = $(`
                                <button class="btn btn-primary kt-btn btn-sm kt-btn--icon search-action" style="margin: 6%;" data-toggle="tooltip" data-theme="dark" title="Search">
							        <span>
							            <i class="la la-search pr-0"></i>							            
							        </span>
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
                    var hideSearchColumnResponsive = function() {
                        thisTable.api().columns().every(function() {
                            var column = this
                            if (column.responsiveHidden()) {
                                $(rowFilter).find('th').eq(column.index()).show();
                            } else {
                                $(rowFilter).find('th').eq(column.index()).hide();
                            }
                        })
                    };

                    // init on datatable load
                    hideSearchColumnResponsive();
                    // recheck on window resize
                    window.onresize = hideSearchColumnResponsive;

                    $('#kt_datepicker_1,#kt_datepicker_2').datepicker();
                },
                columnDefs: [{
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var actions = '';
                            @permission('view-sub-admin')
                            actions += `<a href="/sub-admins/view/${data.id}" class="btn btn-sm btn-clean btn-icon view-icon" data-toggle="tooltip" data-theme="dark" title="View details">
                                    <i class="la la-eye"></i>
                                </a>`;
                            @endpermission
                            @permission('edit-sub-admin')
                            actions += `<a href="/sub-admins/edit/${data.id}" class="btn btn-sm btn-clean btn-icon edit-icon" data-toggle="tooltip" data-theme="dark" title="Edit details">
                                    <i class="la la-edit"></i>
                                </a>`;
                            @endpermission
                            @permission('delete-sub-admin')
                            actions += `<a href="/sub-admins/delete/${data.id}" class="btn btn-sm btn-clean btn-icon delete-record" data-toggle="tooltip" data-theme="dark" title="Delete">
                                    <i class="la la-trash"></i>
                                </a>`;
                            @endpermission

                            return actions;
                            // if (data.role_id == 2) {
                            //     return '<a href="/sub-admins/view/' + data.id + '" class="btn btn-sm btn-clean btn-icon" data-toggle="tooltip" data-theme="dark" title="View details">\
                            //         \<i class="la la-eye"></i>\
                            //     \</a>\
                            //     \<a href="/sub-admins/edit/' + data.id + '" class="btn btn-sm btn-clean btn-icon" data-toggle="tooltip" data-theme="dark" title="Edit details">\
                            //         \<i class="la la-edit"></i>\
                            //     \</a>\
                            //     \<a href="/sub-admins/delete/' + data.id + '" class="btn btn-sm btn-clean btn-icon delete-record" data-toggle="tooltip" data-theme="dark" title="Delete">\
                            //         \<i class="la la-trash"></i>\
                            //     \</a>\
                            //     ';
                            // } else {
                            //     return '<a href="/sub-admins/view/' + data.id + '" class="btn btn-sm btn-clean btn-icon" data-toggle="tooltip" data-theme="dark" title="View details">\
                            //         \<i class="la la-eye"></i>\
                            //     \</a>\
                            //     ';
                            // }

                        },
                    },
                    {
                        "targets": [0],
                        "orderable": false
                    },
                    {
                        targets: 4,
                        render: function(data, type, full, meta) {
                            var status = {
                                1: {
                                    'title': 'Yes',
                                    'class': ' label-light-primary'
                                },
                                0: {
                                    'title': 'No',
                                    'class': 'label-light-danger'
                                },
                            };
                            if (typeof status[data] === 'undefined') {
                                return data;
                            }
                            return '<span class="label label-lg font-weight-bold' + status[data].class + ' label-inline">' + status[data].title + '</span>';
                        },
                    },
                    {
                        targets: 5,
                        render: function(data, type, full, meta) {
                            var status = {
                                1: {
                                    'title': 'Active',
                                    'class': ' label-light-primary'
                                },
                                0: {
                                    'title': 'Inactive',
                                    'class': 'label-light-danger'
                                },
                            };
                            if (typeof status[data] === 'undefined') {
                                return data;
                            }
                            return '<span class="label label-lg font-weight-bold' + status[data].class + ' label-inline">' + status[data].title + '</span>';
                        },
                    },
                    // {
                    //     targets: 5,
                    //     width: '150px',
                    // },

                    // {
                    //     targets: 7,
                    //     render: function(data, type, full, meta) {
                    //         var status = {
                    //             1: {
                    //                 'title': 'Online',
                    //                 'state': 'danger'
                    //             },
                    //             2: {
                    //                 'title': 'Retail',
                    //                 'state': 'primary'
                    //             },
                    //             3: {
                    //                 'title': 'Direct',
                    //                 'state': 'success'
                    //             },
                    //         };
                    //         if (typeof status[data] === 'undefined') {
                    //             return data;
                    //         }
                    //         return '<span class="label label-' + status[data].state + ' label-dot mr-2"></span>' +
                    //             '<span class="font-weight-bold text-' + status[data].state + '">' + status[data].title + '</span>';
                    //     },
                    // },
                ],
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

            $(document).on('click', '.delete-record', function(e) {
                e.preventDefault();
                $me = $(this);
                $.ajax({
                    type: 'POST',
                    url: $me.attr('href'),
                    data: {'check_allowance': 1},
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data > 0) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'This admin has associated with ' + data + ' tickets, so first close all tickets and after delete the admin.',
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
                                    // window.location.href = $me.attr('href');
                                }
                            });
                        }
                    },
                    error: function(data) {
                        toastr.error("Status has not been updated.");
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
    });
</script>
@endsection