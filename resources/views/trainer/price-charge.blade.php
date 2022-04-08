<!--begin: Wizard Step 4-->
<div class="pb-5" data-wizard-type="step-content">
	<h4 class="mb-10 font-weight-bold text-dark">{{ __('Setup Price') }}</h4>
	<div class="form-group row">
		<div class="col-lg-6">
			{{ Form::label('training_price', 'Training <span style="color: red">*</span>','',false ) }}
            {{ Form::number('training_price', null, ['min' => '0','class' => 'form-control '.($errors->has('training_price') ? 'is-invalid' : ''), 'placeholder' => 'Enter training price', 'id' => 'training-price']) }}
            @error('training_price')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
		</div>
		<div class="col-lg-6">
			{{ Form::label('job_support_price', 'Job Support <span style="color: red">*</span>','',false ) }}
            {{ Form::number('job_support_price', null, ['min' => '0','class' => 'form-control '.($errors->has('job_support_price') ? 'is-invalid' : ''), 'placeholder' => 'Enter job support price', 'id' => 'job-support-price']) }}
            @error('job_support_price')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
		</div>
	</div>
	<div class="form-group row">
		<div class="col-lg-6">
			{{ Form::label('interview_support_price', 'Interview Support <span style="color: red">*</span>','',false ) }}
            {{ Form::number('interview_support_price', null, ['min' => '0','class' => 'form-control '.($errors->has('interview_support_price') ? 'is-invalid' : ''), 'placeholder' => 'Enter interview support price', 'id' => 'interview-support-price']) }}
            @error('interview_support_price')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
		</div>
		<div class="col-lg-6">
		</div>
	</div>
</div>
<!--end: Wizard Step 4-->