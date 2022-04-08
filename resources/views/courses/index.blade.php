{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('courses') }}
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
            <h3 class="card-label">{{ __('Course Listing') }}</h3>
        </div>
        <div class="card-toolbar">
            <!--begin::Button-->
            <a href="{{ route('courses.create') }}" class="btn btn-primary font-weight-bolder" data-toggle="tooltip" data-theme="dark" title="{{ __('Add course') }}">
                <i class="la la-plus"></i>{{ __('Add course') }}</a>
            <!--end::Button-->
        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <table class="table table-separate table-head-custom table-checkable" id="subadmin_datatble">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category name</th>
                    <th>Price</th>
                    <th>Special price</th>
                    <th>Status</th>
                    <th>Actions</th>
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
                    url: '/courses/get-list',
                    type: 'GET',
                    data: '_token = <?php echo csrf_token() ?>',
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'course_categories_name'
                    },
                    {
                        data: 'price_with_currency_label'
                    },
                    {
                        data: 'special_price_with_currency_label'
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
                            case 'Name':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Category name':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Price':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Special price':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
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
							            <i class="la la-search"></i>							            
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
                },
                columnDefs: [{
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            return '<a href="/courses/view/' + data.id + '" class="btn btn-sm btn-clean btn-icon view-icon" data-toggle="tooltip" data-theme="dark" title="View details">\
                    			\<i class="la la-eye"></i>\
                    		\</a>\
                            \<a href="/courses/edit/' + data.id + '" class="btn btn-sm btn-clean btn-icon edit-icon" data-toggle="tooltip" data-theme="dark" title="Edit details">\
                    			\<i class="la la-edit"></i>\
                    		\</a>\
                    		\<a href="/courses/delete/' + data.id + '" class="btn btn-sm btn-clean btn-icon delete-record" data-toggle="tooltip" data-theme="dark" title="Delete">\
                    			\<i class="la la-trash"></i>\
                    		\</a>\
                    	';
                        },
                    },
                    {
                        "targets": [0],
                        "orderable": false
                    },
                    {
                        targets: 1,
                        render: function(data, type, full, meta) {
                            return (data != null || data != '') ? setCourseData(data) : '<span>--</span>';
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
                ],
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    var oSettings = this.fnSettings();
                    $("td:first", nRow).html(oSettings._iDisplayStart + iDisplayIndex + 1);
                    return nRow;
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
                                text: 'This course has associated with ' + data + ' tickets, so first close all tickets and after delete the course.',
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