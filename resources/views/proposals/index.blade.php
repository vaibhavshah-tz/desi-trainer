{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('proposals', request()->route('id')) }}
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
            <h3 class="card-label">{{ __('Proposal Listing') }}</h3>
        </div>
        <div class="card-toolbar">
            <!--begin::Button-->
            <a href="{{ route('tickets.proposals.create', request()->route('id')) }}" class="btn btn-primary font-weight-bolder" data-toggle="tooltip" data-theme="dark" title="{{ __('Create New Proposal') }}">
                <i class="la la-plus"></i>{{ __('Create New Proposal') }}</a>
            <!--end::Button-->
        </div>
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <div class="table-responsive">
            <table class="table table-separate table-head-custom table-checkable" id="proposal_datatable">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th>Name</th>
                        <th width="20%">Quote</th>
                        <th width="20%">Accepted Trainers</th>
                        <th class="action" width="15%">Actions</th>
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

        var proposalTable = function() {

            // begin first table
            var table = $('#proposal_datatable').DataTable({
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
                    url: "{{route('tickets.proposals.getlist', ['id' => request()->route('id')])}}",
                    type: 'GET',
                    data: function(data) {
                        $('.datatable-input').each(function(e) {
                            data[$(this).attr('name')] = this.value;
                        });

                        return data;
                    },
                },
                columnDefs: [{
                    targets: [0, 3, -1],
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
            $(document).on('click', '.delete-record', function(e) {
                e.preventDefault();
                $me = $(this);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "It will permanently deleted !",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Yes, delete it!",
                }).then(function(data) {
                    if (data.isConfirmed) {
                        window.location.href = $me.attr('href');
                    }
                });
            });
        };

        return {
            //main function to initiate the module
            init: function() {
                proposalTable();
            },

        };
    }();

    jQuery(document).ready(function() {
        KTDatatablesSearchOptionsColumnSearch.init();
    });
</script>
@endsection