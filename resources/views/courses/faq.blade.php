<div id="pane-d" class="card tab-pane fade" role="tabpanel" aria-labelledby="tab-d">

    <div id="collapse-d" class="collapse" role="tabpanel" data-parent="#content" aria-labelledby="heading-d">
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder addNewFaq" data-toggle="tooltip" data-theme="dark" title="" data-original-title="{{ __('Add New FAQ') }}">
                    <i class="la la-plus"></i>{{ __('Add New FAQ') }}</a>
            </div>

            <!-- Start:Add section 1 -->
            <div class="errors"></div>

            <div class="sec-box faq-form">
                {{ Form::open(['route' => 'courses.save.faq' ,'name' => 'faq-form','id' => 'faq-form', 'enctype' => 'multipart/form-data']) }}
                <div class="row">
                    <div class="form-group col-xl-12">
                        {{ Form::label('question', __('Question')) }}
                        {{ Form::text('title',null, ['class' => 'form-control faqTitleTextbox', 'placeholder' => 'Enter question']) }}
                    </div>
                    <div class="form-group col-xl-12">
                        {{ Form::label('answer', __('Answer')) }}
                        {{ Form::textarea('description',null, ['class' => 'form-control faqDescriptionTextbox','rows' => 5, 'style' => 'resize: none;', 'placeholder' => 'Enter answer']) }}
                    </div>
                </div>
                <div class="mt-2 d-flex justify-content-end">
                    {{ Form::button(__('Save'), ['class' => 'btn btn-primary saveFaq', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Save')]) }}
                    {{ Form::button(__('Cancel'), ['type' => 'reset','id' => 'reset','class' => 'btn btn-danger ml-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Cancel')]) }}
                </div>
                <input type="hidden" name="faqId" id="faqId">
                {{ Form::close() }}
            </div>

            <div class="faqListing">
            </div>
            <!-- End:Add section 1 -->
            <div class="mt-5">
                {{ Form::button(__('Previous'), ['class' => 'btn btn-primary btnPrevious', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Previous'), 'data-id' => 'tab-C']) }}
                <a href="{{ route('courses.index') }}" class="btn btn-danger ml-2" data-toggle="tooltip" data-theme="dark" title="{{ __('Cancel') }}">{{ __('Cancel') }}</a>
            </div>
        </div>
    </div>
</div>