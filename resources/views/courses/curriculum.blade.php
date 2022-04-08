<div id="pane-C" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-C">
    <div class="card-header" role="tab" id="heading-C">
        <h5 class="mb-0">
            <a class="collapsed" data-toggle="collapse" href="#collapse-C" aria-expanded="false" aria-controls="collapse-C">
                {{ __('Course Curriculum') }}
            </a>
        </h5>
    </div>
    <div id="collapse-C" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-C">
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <a href="javascript:void(0)" id="addNewSection" class="btn btn-primary font-weight-bolder" data-toggle="tooltip" data-theme="dark" title="" data-original-title="{{ __('Add New Section') }}">
                    <i class="la la-plus"></i>{{ __('Add New Section') }}</a>
            </div>
            <!-- Start:Add section 1 -->
            <div class="errors"></div>

            <div class="sec-box section-form">
                @if(isset($courseCurriculum))
                {{ Form::model($courseCurriculum, ['route' => ['courses.curriculum'], 'method' => 'patch','id' => 'course-section', 'enctype' => 'multipart/form-data']) }}
                @else
                {{ Form::open(['route' => 'courses.curriculum' ,'name' => 'course-section','id' => 'course-section', 'enctype' => 'multipart/form-data']) }}
                @endif
                <div class="row">
                    <div class="form-group col-xl-12">
                        {{ Form::label('section_name', __('Section Name')) }}
                        {{ Form::text('title',null, ['class' => 'form-control sectionNameTextbox', 'placeholder' => 'Enter section name']) }}
                    </div>
                    <div class="form-group col-xl-12">
                        {{ Form::label('section_description', __('Section Description')) }}
                        {{ Form::textarea('description',null, ['class' => 'form-control sectionDescriptionTextbox','rows' => 5, 'style' => 'resize: none;', 'placeholder' => 'Enter section description']) }}
                    </div>
                </div>
                <div class="mt-2 d-flex justify-content-end">
                    {{ Form::button('Save', ['class' => 'btn btn-primary saveSection', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => 'Save']) }}
                    {{ Form::button('Cancel', ['type' => 'reset','id' => 'reset','class' => 'btn btn-danger ml-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => 'Cancel']) }}
                </div>
                <input type="hidden" name="curriculumId" id="curriculumId">
                {{ Form::close() }}
            </div>

            <div class="sectionListing">
            </div>
            <!-- End:Add section 1 -->

            <div class="mt-7">
                {{ Form::button(__('Previous'), ['class' => 'btn btn-primary btnPrevious', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Previous'), 'data-id' => 'tab-B']) }}
                {{ Form::button( __('Next'), ['class' => 'btn btn-primary ml-2 btnNext', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Next'), 'data-id' => 'tab-D']) }}
                <a href="{{ route('courses.index') }}" class="btn btn-danger ml-2" data-toggle="tooltip" data-theme="dark" title="{{ __('Cancel') }}">{{ __('Cancel') }}</a>
            </div>
        </div>

    </div>
</div>