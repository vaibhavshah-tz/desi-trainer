{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('interested-trainers', request()->route('id')) }}
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
            <h3 class="card-label">{{ __('Interested Trainers Listing') }}</h3>
        </div>
        <div class="card-toolbar">

        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <div class="table-responsive">
            <table class="table table-separate table-head-custom table-checkable" id="interested_trainers_datatable">
                <thead>
                    <tr>
                        <th width="5%">{{ __('ID') }}</th>
                        <th width="20%">{{ __('Name') }}</th>
                        <th width="20%">{{ __('Email') }}</th>
                        <th width="15%">{{ __('Title') }}</th>
                        <!-- <th width="20%">{{ __('Category') }}</th> -->
                        <th width="20%">{{ __('Primary Skills') }}</th>
                        <th width="10%">{{ __('Total Exp.') }}</th>
                        <th class="action" width="15%">{{ __('Actions') }}</th>
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
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
<!-- <script src="{{ asset('plugins/custom/nexmo-client/nexmoClient.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/call.js') }}" type="text/javascript"></script> -->
<script>
    var KTDatatablesSearchOptionsColumnSearch = function() {

        $.fn.dataTable.Api.register('column().title()', function() {
            return $(this.header()).text().trim();
        });

        var interestedTrainersTable = function() {
            // begin first table
            var table = $('#interested_trainers_datatable').DataTable({
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
                    url: "{{route('tickets.interested-trainers.getlist', ['id' => request()->route('id')])}}",
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
                            loadCallJs();
                        }
                    });
                    var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));
                    this.api().columns().every(function() {
                        var column = this;
                        var input;
                        switch (column.title()) {
                            case 'ID':
                                // input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/>`);
                                break;
                            case 'Name':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Email':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Title':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                                // case 'Category':
                                //     input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                //     break;
                            case 'Primary Skills':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
                                break;
                            case 'Total Exp.':
                                input = $(`<div class="serch-box"><i class="la la-search"></i><input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `"/></div>`);
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
                                    loadCallJs();
                                });

                                $(reset).on('click', function(e) {
                                    e.preventDefault();
                                    $(rowFilter).find('.datatable-input').each(function(i) {
                                        $(this).val('');
                                        table.column($(this).data('col-index')).search('', false, false);
                                    });
                                    table.table().draw();
                                    loadCallJs();
                                });
                                break;
                        }
                        if (column.title() != 'Actions') {
                            $(input).appendTo($('<th>').appendTo(rowFilter));
                        }
                    });

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
                        targets: [0, 4, 6],
                        orderable: false
                    },
                    {
                        targets: 3,
                        render: function(data, type, full, meta) {
                            return (data != null || data != '') ? setCourseData(data) : '<span>--</span>';
                        },
                    },
                    {
                        targets: 4,
                        "orderable": false,
                        render: function(data, type, full, meta) {
                            var primarySkills = [];
                            if (data.length > 0) {
                                $.each(data, function(i, item) {
                                    var name = '<span class="label label-light-primary text-nowrap label-inline mr-1 mb-1 custom-label">' + item.name + '</span>';
                                    primarySkills.push(name);
                                });
                            }

                            return (primarySkills.length > 0) ? primarySkills.join('') : '<span>--</span>';
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
        };

        return {
            //main function to initiate the module
            init: function() {
                interestedTrainersTable();
            },
        };
    }();

    jQuery(document).ready(function() {
        KTDatatablesSearchOptionsColumnSearch.init();
    });
</script>
@endsection