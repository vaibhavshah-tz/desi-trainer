@error('ids')
<div class="alert alert-danger">
    <ul>
        <li>{{ $message }}</li>
    </ul>
</div>
@enderror
<div class="card-body">
    <div class="form-group row">
        {{ Form::hidden('ticket_id', request()->route('id'), ['id' => 'ticket_id']) }}
        <div class="col-lg-6">
            {{ Form::label('name' , 'Name <span style="color: red">*</span>','',false ) }}
            {{ Form::text('name', null, ['class' => 'form-control '.($errors->has('name') ? 'is-invalid' : ''), 'placeholder' => 'Enter name']) }}
            @error('name')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
        <div class="col-lg-2">
            {{ Form::label('currency', 'Currency <span style="color: red">*</span>','',false ) }}
            {{ Form::select('currency', CommonHelper::getCurrency(), null, ['class' => 'form-control '.($errors->has('currency') ? 'is-invalid' : ''), (isset($proposal) && ($proposal->trainers_count > 0)) ? 'disabled' : '', 'id' => 'proposal-currency']) }}
            @error('currency')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
        <div class="col-lg-4">
            {{ Form::label('quote', 'Quote <span style="color: red">*</span>','',false ) }}
            {{ Form::number('quote',null, ['min' => '0', 'class' => 'form-control '.($errors->has('quote') ? 'is-invalid' : ''), 'placeholder' => 'Enter quote', (isset($proposal) && ($proposal->trainers_count > 0)) ? 'disabled' : '', 'id' => 'proposal-quote']) }}
            @error('quote')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-12">
			{{ Form::label('description', 'Description <span style="color: red">*</span>','',false ) }}
			{{ Form::textarea('description', null, ['class' => 'form-control '.($errors->has('description') ? 'is-invalid' : ''), 'placeholder' => 'Enter description', 'rows' => 3]) }}
			@error('description')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
			@enderror
		</div>
    </div>
    @if(isset($proposal) && ($proposal->trainers_count > 0))
        {{ Form::hidden('currency', $proposal->currency) }}
        {{ Form::hidden('quote', $proposal->quote) }}
    @endif
    @if(!isset($proposal))
    <h5 class="text-dark font-weight-bold">{{ __('Trainers:') }}</h5>
    <div class="separator separator-dashed my-8"></div>
    <div class="form-group row mb-6">
        <div class="col-lg-4 mb-lg-0 mb-6">
            <input type="text" class="form-control datatable-input" name="search" placeholder="Search by:name,skills,category,title" data-col-index="0" />
        </div>
        <div class="col-lg-3 mb-lg-0 mb-6 col-form-label">
            <div class="checkbox-inline">
                <label class="checkbox checkbox-primary">
                    <input type="checkbox" class="datatable-input" name="recommended_trainer" value="1" data-col-index="2"><span></span>Show Recommended Trainer
                </label>
            </div>
        </div>
        <div class="col-lg-3 mb-lg-0 mb-6 col-form-label">
            <div class="checkbox-inline">
                <label class="checkbox checkbox-primary">
                    <input type="checkbox" class="datatable-input" name="interested_trainer" value="1" data-col-index="1"><span></span>Show Interested Trainer
                </label>
            </div>
        </div>
        <div class="col-lg-2 mb-lg-0 mb-6 col-form-label">
            <button class="btn btn-primary kt-btn btn-sm kt-btn--icon pr-2"  id="search">
                <span>
                    <span>Search</span>
                </span>
            </button>
            <button class="btn btn-secondary kt-btn btn-sm kt-btn--icon pr-2" id="reset">
                <span>
                    <span>Reset</span>
                </span>
            </button>
        </div>
    </div>
    <!--begin: Datatable-->
    <div class="table-responsive">
        <table class="table table-separate table-head-custom table-checkable" id="trainer_datatable">
            <thead>
                <tr>
                    <th width="5%"><input name="select_all" value="1" type="checkbox"></th>
                    <th>Name</th>
                    <th>Price</th>
                    <th width="15%">Title</th>
                    <th width="15%">Skills</th>
                    <th width="15%">Category</th>
                    <th width="15%">Total Exp.</th>
                </tr>
            </thead>
        </table>
    </div>
    <!--end: Datatable-->
    @endif
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-lg-12 text-right">
            {{ Form::submit('Send Proposal', ['class' => 'btn btn-primary mr-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => 'Send Proposal']) }}
            {{ Form::button('Reset', ['type' => 'reset','id' => 'form-reset','class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => 'Reset']) }}
        </div>
    </div>
</div>

{{-- Style Section --}}
@section('styles')
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<style>
    table.dataTable tbody tr,
    table.dataTable thead th:first-child {
        cursor: pointer;
    }
</style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script>
    var checkQuoteAmount = "{{ route('tickets.proposals.check-quote', ['id' => request()->route('id')]) }}";
</script>
<script src="{{ asset('js/pages/widgets.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/proposal.js') }}" type="text/javascript"></script>
<script>
    // Updates "Select all" control in a data table
    function updateDataTableSelectAllCtrl(table){
        var $table             = table.table().node();
        var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
        var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
        var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

        // If none of the checkboxes are checked
        if($chkbox_checked.length === 0){
            chkbox_select_all.checked = false;
            if('indeterminate' in chkbox_select_all){
                chkbox_select_all.indeterminate = false;
            }

        // If all of the checkboxes are checked
        } else if ($chkbox_checked.length === $chkbox_all.length){
            chkbox_select_all.checked = true;
            if('indeterminate' in chkbox_select_all){
                chkbox_select_all.indeterminate = false;
            }

        // If some of the checkboxes are checked
        } else {
            chkbox_select_all.checked = true;
            if('indeterminate' in chkbox_select_all){
                chkbox_select_all.indeterminate = true;
            }
        }
    }
    // Array holding selected row IDs
    var rows_selected = [];
    var KTDatatablesSearchOptionsColumnSearch = function() {

        $.fn.dataTable.Api.register('column().title()', function() {
            return $(this.header()).text().trim();
        });

        var trainerTable = function() {

            var table = $('#trainer_datatable').DataTable({
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
                'rowCallback': function(row, data, dataIndex){
                    // Get row ID
                    var rowId = $(data[0]).val();

                    // If row ID is in the list of selected row IDs
                    if($.inArray(rowId, rows_selected) !== -1){
                        $(row).find('input[type="checkbox"]').prop('checked', true);
                        $(row).addClass('selected');
                    }
                },
                ajax: {
                    url: "{{route('tickets.proposals.get-trainer-list', ['id' => request()->route('id')])}}",
                    type: 'GET',
                    data: function(data) {
                        $('.datatable-input').each(function(e) {
                            if($(this).is(':checkbox') && $(this).is(':checked')) {
                                data[$(this).attr('name')] = this.value;
                            } 
                            if($(this).attr('type') == 'text') {
                                data[$(this).attr('name')] = this.value;
                            }
                        });

                        return data;
                    },
                },
                columnDefs: [
                    {
                        targets: [0,1,2,3,4,5,6],
                        orderable: false
                    }
                ],
            });

            // Handle click on checkbox
            $('#trainer_datatable tbody').on('click', 'input[type="checkbox"]', function(e){
                var $row = $(this).closest('tr');
                var data = table.row($row).data();
                var rowId = $(data[0]).val();
                var index = $.inArray(rowId, rows_selected);

                if(this.checked && index === -1){
                    rows_selected.push(rowId);
                } else if (!this.checked && index !== -1){
                    rows_selected.splice(index, 1);
                }

                if(this.checked){
                    $row.addClass('selected');
                } else {
                    $row.removeClass('selected');
                }
                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(table);

                e.stopPropagation();
            });

            // Handle click on table cells with checkboxes
            $('#trainer_datatable').on('click', 'tbody td, thead th:first-child', function(e){
                $(this).parent().find('input[type="checkbox"]').trigger('click');
            });

            // Handle click on "Select all" control
            $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
                if(this.checked){
                    $('#trainer_datatable tbody input[type="checkbox"]:not(:checked)').trigger('click');
                } else {
                    $('#trainer_datatable tbody input[type="checkbox"]:checked').trigger('click');
                }

                e.stopPropagation();
            });

            table.on('draw', function(){
                updateDataTableSelectAllCtrl(table);
            });

            // $('.datatable-input').on('keypress', function(e) {
            //     if(e.keyCode == 13) {
            //         table.table().draw();
            //         e.preventDefault();
            //         return false;
            //     }
            // });

            // $('.datatable-input').on('change', function(e) {
            //     table.table().draw();
            // });

            $('#search').on('click', function(e) {
                e.preventDefault();
                table.table().draw();
            });

            $('#reset').on('click', function(e) {
                e.preventDefault();
                $('.datatable-input').each(function(i) {
                    if($(this).is(':checkbox')) {
                        $(this).prop('checked', false)
                    } 
                    if($(this).attr('type') == 'text') {
                        $(this).val('');
                    }
                });
                table.table().draw();
            });

            $('#form-reset').on('click', function(e) {
                $('#trainer_datatable tbody input[type="checkbox"]:checked').trigger('click');
                table.table().draw();
            })
        };

        return {
            //main function to initiate the module
            init: function() {
                trainerTable();
            },

        };
    }();

    jQuery(document).ready(function() {
        KTDatatablesSearchOptionsColumnSearch.init();
    });
</script>
@endsection