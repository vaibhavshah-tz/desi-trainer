<div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap ">
    <div class="d-flex align-items-center flex-wrap mr-1"></div>
    <div class="d-flex align-items-center">
        <div class="row gutter-b">
            @php $subAdmin = 0; @endphp
            @if(auth()->user()->role_id === config('constants.ADMIN_ROLE.SUPER_ADMIN'))
            @php $subAdmin = 1; @endphp
            <div class="col-lg-3">
                {{ Form::select('sub_admin', $subAdmins, $adminSearch, ['class' => 'filter form-control has-error', 'placeholder' => 'All Staff', 'id' => 'subAdmin']) }}
            </div>
            @endif
            <div class="col-lg-{{ ($subAdmin) ? 4 : 6}} col-md-9 col-sm-12">
                <div class="input-daterange input-group date" id="kt_datepicker_5">
                    {{ Form::text('start_date', $startDate, ['class' => 'filter form-control', 'placeholder' => 'Start date', 'id' => 'start_date']) }}
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-ellipsis-h"></i>
                        </span>
                    </div>
                    {{ Form::text('end_date', $endDate, ['class' => 'filter form-control', 'placeholder' => 'End date', 'id' => 'end_date']) }}
                </div>
            </div>
            <div class="col-lg-{{ ($subAdmin) ? 3 : 4}}">
                {{ Form::select('start_date', CommonHelper::dateFilter(), $dateStringSearch, ['class' => 'filter form-control has-error', 'placeholder' => 'Select search date', 'id' => 'date_string']) }}
            </div>

            <div class="col-lg-2">
                <button id="reset" class="btn btn-secondary kt-btn btn-sm kt-btn--icon" style="margin: 1%;" data-toggle="tooltip" data-theme="dark" title="{{ __('Reset') }}">
                    <span>
                        <i class="la la-close pr-0"></i>{{ __('Reset') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>