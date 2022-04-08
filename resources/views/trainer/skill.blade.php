<!--begin: Wizard Step 3-->
<div class="pb-5" data-wizard-type="step-content">
    <h4 class="mb-10 font-weight-bold text-dark">{{ __('Setup Your Skills') }}</h4>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('skill_title', 'Title <span style="color: red">*</span>','',false ) }}
            {{ Form::text('skill_title',null, ['class' => 'form-control', 'placeholder' => 'Enter title']) }}
            @error('skill_title')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('ticket_type', 'Interested in <span style="color: red">*</span>','',false ) }}
            {{ Form::select('ticket_type[]', array_column(App\Models\TicketType::getAllTicketType(), 'name','id'), isset($trainer) ? $trainer->ticketTypes : null, ['class' => 'form-control select2 ticket_type', 'id' => 'ticket_type', 'multiple' => 'multiple']) }}
            @error('ticket_type')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('total_experience_year', 'Total Experience Year <span style="color: red">*</span>','',false ) }}
            {{ Form::select('total_experience_year', CommonHelper::getYearList(), null, ['class' => 'form-control select2', 'placeholder' => 'Select Experience Year', 'id' => 'total_experience_year']) }}
            @error('total_experience_year')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('total_experience_month', 'Total Experience Month','',false ) }}
            {{ Form::select('total_experience_month', CommonHelper::getMonthList(), null, ['class' => 'form-control select2', 'placeholder' => 'Select Experience Month', 'id' => 'total_experience_month']) }}
            @error('total_experience_month')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('course_category_id', 'Course Category <span style="color: red">*</span>','',false ) }}
            {{ Form::select('course_category_id', array_column(App\Models\CourseCategory::getAllCourseCategory(), 'name','id'), null, ['class' => 'form-control select2', 'placeholder' => 'Select Course category', 'id' => 'course_category_id']) }}
            @error('course_category_id')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('primary_skill', 'Primary Skills <span style="color: red">*</span>','',false ) }}
            {{ Form::select('primary_skill[]',isset($primarySkill) ? array_column($primarySkill, 'name','id') : [], isset($trainer) ? $trainer->primarySkills : null, ['class' => 'form-control select2 primary_skill', 'id' => 'primary_skill', 'multiple' => 'multiple']) }}
            @error('primary_skill')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('prior_teaching_experience_year', 'Prior Teaching Experience Year <span style="color: red">*</span>','',false ) }}
            {{ Form::select('prior_teaching_experience_year', CommonHelper::getYearList(), null, ['class' => 'form-control select2', 'placeholder' => 'Select Prior Teaching Experience Year', 'id' => 'prior_teaching_experience_year']) }}
            @error('prior_teaching_experience_year')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('prior_teaching_experience_month', 'Prior Teaching Experience Month','',false ) }}
            {{ Form::select('prior_teaching_experience_month', CommonHelper::getMonthList(), null, ['class' => 'form-control select2', 'placeholder' => 'Select Prior Teaching Experience Month', 'id' => 'prior_teaching_experience_month']) }}
            @error('prior_teaching_experience_month')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-4 up-resume">
            {{ Form::label('resume', 'Upload Resume') }}
            {{ Form::file('resume', ['class' => 'form-control']) }}
            @error('resume')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-2 d-flex mt-6 pt-4">
            @if(!empty($trainer->resume_url))
            <a href="{{ $trainer->resume_url }}" id="view_resume" target="_blank" class="btn btn-light-primary font-weight-bold form-control  p-0 d-flex align-items-center justify-content-center" data-toggle="tooltip" data-theme="dark" title="{{ __('View Resume') }}" download>{{ __('View Resume') }}</a>
            @endif
        </div>

        <div class="col-lg-6 trainer-comment">
            @php $noteData = ''; @endphp
            <div class="d-flex justify-content-between mb-5 align-items-center">
                {{ Form::label('note', 'Comments for Trainer') }}
                <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder addNewComment" data-toggle="tooltip" data-theme="dark" title="" data-original-title="{{ __('Add New Comment') }}">
                    <i class="la la-plus"></i> {{ __('Add New Comment') }} </a>
            </div>
            <table class="table commentList">
                @if(isset($trainer->trainerComments) && $trainer->trainerComments->count())
                @foreach ($trainer->trainerComments as $value)
                @if(Auth::user()->id === $value->user_id)
                @php $noteData = $value->note; @endphp
                @endif
                <tr>
                    <th width="150">{{ $value->user->full_name ?? '' }}</th>
                    <th>{{ $value->note ?? '' }}</th>
                </tr>
                @endforeach
                @else
                <tr>
                    <th width="150"></th>
                    <th>{{ __('No comments found') }}</th>
                </tr>
                @endif
            </table>
            {{ Form::textarea('note',$noteData, ['class' => 'form-control commentBox','cols'=>'5','rows'=>'5', 'placeholder' => 'Enter comments for trainer']) }}
            @error('note')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
</div>
<!--end: Wizard Step 3-->