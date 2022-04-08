<!-- Course detail -->
<div id="pane-A" class="card tab-pane fade show active" role="tabpanel" aria-labelledby="tab-A">
    <div class="card-header" role="tab" id="heading-A">
        <h5 class="mb-0">
            <a data-toggle="collapse" href="#collapse-A" aria-expanded="true" aria-controls="collapse-A">
                {{ __('Course Detail') }}
            </a>
        </h5>
    </div>
    <div id="collapse-A" class="collapse show" data-parent="#content" role="tabpanel" aria-labelledby="heading-A">
        <div class="card-body">
            <div class="errors"></div>
            @if(isset($courseDetails))
            {{ Form::model($courseDetails, ['route' => ['courses.save.details'], 'method' => 'patch','id' => 'course-details', 'enctype' => 'multipart/form-data']) }}
            @else
            {{ Form::open(['route' => 'courses.save.details' ,'name' => 'course-details','id' => 'course-details', 'enctype' => 'multipart/form-data']) }}
            @endif
            <div class="kyc-frm">
                <div class="kyc-frm-inner d-flex row">
                    <div class="form-group col-xl-6">
                        {{ Form::label('course_category_id', 'Course Category <span style="color: red">*</span>','',false ) }}
                        {{ Form::select('course_category_id', array_column(App\Models\CourseCategory::getAllCourseCategory(), 'name','id'), null, ['class' => 'form-control select2','placeholder' => 'Select course category', 'id' => 'courseCategory']) }}
                        @error('course_category_id')
                        @component('components.serverValidation')
                        {{ $message }}
                        @endcomponent
                        @enderror
                    </div>
                    <div class="form-group col-xl-6">
                        {{ Form::label('name', 'Course Name <span style="color: red">*</span>','',false ) }}
                        {{ Form::text('name',null, ['class' => 'form-control', 'placeholder' => 'Enter name']) }}
                        @error('name')
                        @component('components.serverValidation')
                        {{ $message }}
                        @endcomponent
                        @enderror
                    </div>
                    <div class="form-group col-xl-6">
                        {{ Form::label('trainer_id', 'Trainer','',false ) }}
                        {{ Form::select('trainer_id', $trainer, null, ['class' => 'form-control select2','placeholder' => 'Select trainer', 'id' => 'trainer_id']) }}
                        @error('trainer_id')
                            @component('components.serverValidation')
                                {{ $message }}
                            @endcomponent
                        @enderror
                    </div>
                    <div class="form-group col-xl-6">
                        {{ Form::label('description', 'Description <span style="color: red">*</span>','',false ) }}
                        {{ Form::textarea('description',null, ['class' => 'form-control summernote '.($errors->has('description') ? 'is-invalid' : ''), 'placeholder' => 'Enter description', 'rows' => '5', 'style' => 'resize: none;']) }}
                        @error('description')
                        @component('components.serverValidation')
                        {{ $message }}
                        @endcomponent
                        @enderror
                        <!-- <textarea class="form-control" name="username" placeholder="" required="" autofocus="" rows="5" style="resize: none;" /></textarea> -->
                    </div>
                    <div class="form-group col-xl-4">
                        {{ Form::label('currency', 'Currency <span style="color: red">*</span>','',false ) }}
                        {{ Form::select('currency', CommonHelper::getCurrency(), null, ['class' => 'form-control']) }}
                        @error('currency')
                        @component('components.serverValidation')
                        {{ $message }}
                        @endcomponent
                        @enderror
                        <!-- <input type="number" class="form-control" name="username" placeholder="" required="" autofocus="" /> -->
                    </div>
                    <div class="form-group col-xl-4">
                        {{ Form::label('course_price', 'Course Price <span style="color: red">*</span>','',false ) }}
                        {{ Form::number('course_price',null, ['min' => '0','class' => 'form-control', 'placeholder' => 'Enter course price']) }}
                        @error('course_price')
                        @component('components.serverValidation')
                        {{ $message }}
                        @endcomponent
                        @enderror
                    </div>
                    <div class="form-group col-xl-4">
                        {{ Form::label('course_special_price', 'Course Special Price <span style="color: red">*</span>','',false ) }}
                        {{ Form::number('course_special_price',null, ['min' => '0','class' => 'form-control', 'placeholder' => 'Enter course special price']) }}
                        @error('course_special_price')
                        @component('components.serverValidation')
                        {{ $message }}
                        @endcomponent
                        @enderror
                    </div>
                    <div class="d-flex flex-row align-items-center mb-4 pb-2 col-xl-12">
                        <div class="img-box">
                            <img src="{{ asset($courseDetails->cover_image_url ?? 'media/default/default_course.jpg') }}" id="view_cover_image">
                        </div>
                        <div class="up-img-box">
                            <button class="position-relative" data-toggle="tooltip" data-theme="dark" title="{{ __('Upload Image') }}">{{ __('Upload Image') }}<input type="file" name="cover_image" id="cover_image" class="hide-file"></button>
                            <span>{{ __('All files types. Max 5MB') }}</span>
                        </div>
                    </div>
                    <div class="form-group sel-arrow col-xl-6">
                        {{ Form::label('status', 'Status') }}
                        {{ Form::select('status', CommonHelper::getStatus(), null, ['class' => 'form-control', 'id' => 'kt_select2_1']) }}
                        @error('status')
                        @component('components.serverValidation')
                        {{ $message }}
                        @endcomponent
                        @enderror
                    </div>
                    <div class="form-group sel-arrow col-xl-6">
                        {{ Form::label('trending', 'Trending Course') }}
                        <div class="checkbox-inline">
                            <label class="checkbox">
                                <input type="checkbox" name="trending" value="1" {{ (isset($courseDetails->trending) && $courseDetails->trending )? "checked" : "" }}>
                                <span></span>{{ __('Mark as trending') }}</label>
                        </div>
                        @error('trending')
                        @component('components.serverValidation')
                        {{ $message }}
                        @endcomponent
                        @enderror
                    </div>
                </div>
                <div class="mt-2 kyc-footer">
                    {{ Form::button(__('Save & Next'), ['class' => 'btn btn-primary saveDetails', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'data-id' => 'tab-B', 'title' => __('Save & Next')]) }}
                    <a href="{{ route('courses.index') }}" class="btn btn-danger ml-2" data-toggle="tooltip" data-theme="dark" title="{{ __('Cancel') }}">{{ __('Cancel') }}</a>
                </div>
            </div>
            <input type="hidden" value="{{$courseDetails->id ?? ''}}" name="course-id" id="course-id">
            {{ Form::close() }}
        </div>
    </div>
</div>