<div class="col-lg-6">
    <!--begin::List Widget 3-->
    <div class="card card-custom card-stretch dashboard-course-div">
        <!--begin::Header-->
        <div class="card-header border-0">
            <h3 class="card-title font-weight-bolder text-dark">{{ __('Total courses') }}</h3>
            <div class="card-toolbar">
                <div class="dropdown dropdown-inline">
                    <!-- <a href="#" class="btn btn-light-primary btn-sm font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">August</a> -->
                    <!-- <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"> -->
                    {{ Form::select('courseStatus', CommonHelper::getStatus(), null, ['class' => 'form-control datatable-input', 'placeholder' => 'Filter', 'id' => 'filterCourseStatus']) }}
                    <!-- </div> -->
                </div>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body pt-2">
            <!--begin: Datatable-->
            <div class="table-responsive">
                <table class="table table-separate table-head-custom table-checkable" id="course_datatable">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Special Price') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!--end: Datatable-->
        </div>
        <!--end::Body-->
    </div>
    <!--end::List Widget 3-->
</div>