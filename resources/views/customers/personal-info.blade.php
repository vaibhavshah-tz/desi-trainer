<div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
    <h4 class="mb-10 font-weight-bold text-dark">Setup personal information</h4>
    <div class="form-group row">
        {{ Form::hidden('user_id', $user->id ?? 0, ['id' => 'user-id']) }}
        <div class="col-lg-6">
            {{ Form::label('first_name', 'First Name <span style="color: red">*</span>','',false) }}
            {{ Form::text('first_name',null, ['class' => 'form-control '.($errors->has('first_name') ? 'is-invalid' : ''), 'placeholder' => 'Enter first name']) }}
            @error('first_name')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('last_name', 'Last Name <span style="color: red">*</span>','',false) }}
            {{ Form::text('last_name',null, ['class' => 'form-control '.($errors->has('last_name') ? 'is-invalid' : ''), 'placeholder' => 'Enter last name']) }}
            @error('last_name')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('email', 'Email <span style="color: red">*</span>','',false) }}
            {{ Form::email('email',null, ['class' => 'form-control '.($errors->has('email') ? 'is-invalid' : ''), 'placeholder' => 'Enter email']) }}
            @error('email')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <!-- <div class="col-lg-6">
            {{ Form::label('username', __('Username')) }}
            {{ Form::text('username',null, ['class' => 'form-control '.($errors->has('username') ? 'is-invalid' : ''), 'placeholder' => 'Enter username']) }}
            @error('username')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div> -->
        <div class="col-lg-6">
            {{ Form::label('status', 'Status <span style="color: red">*</span>','',false) }}
            {{ Form::select('status', CommonHelper::getStatus(), null, ['class' => 'form-control kt-select2 select2 '.($errors->has('status') ? 'is-invalid' : ''), 'id' => 'kt_select2_1']) }}
            @error('status')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('user_type', 'User Type <span style="color: red">*</span>','',false) }}
            {{ Form::select('user_type', CommonHelper::getCustomerType(), null, ['class' => 'form-control select2 '.($errors->has('user_type') ? 'is-invalid' : ''), 'placeholder' => 'Select user type', 'id' => 'user_type']) }}
            @error('user_type')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            <label>{{ __('Gender') }} <span style="color: red">*</span></label>
            <div class="radio-inline">
                <label class="radio radio-lg">
                    {{ Form::radio('gender', '1', true) }}
                    <span></span>Male</label>
                <label class="radio radio-lg">
                    {{ Form::radio('gender', '2', null) }}
                    <span></span>Female</label>
            </div>
            @error('gender')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <!-- <div class="form-group row" id="company-details" style="{{ (!empty($user->user_type) && $user->user_type == config('constants.CUSTOMER_TYPE.EMPLOYER')) ? '' : 'display:none;' }}"> -->

        <!-- <div class="col-lg-6">
            {{ Form::label('company_address', __('Company Address')) }}
            {{ Form::textarea('company_address',null, ['class' => 'form-control '.($errors->has('company_address') ? 'is-invalid' : ''), 'placeholder' => 'Enter company address', 'rows' => '3']) }}
            @error('company_address')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div> -->
        <!-- </div> -->
        <div class="col-lg-6" id="company-details" style="{{ (!empty($user->user_type) && $user->user_type == config('constants.CUSTOMER_TYPE.EMPLOYER')) ? '' : 'display:none;' }}">
            {{ Form::label('company_name', 'Company name / Employer name <span style="color: red">*</span>','',false) }}
            {{ Form::text('company_name',null, ['class' => 'form-control '.($errors->has('company_name') ? 'is-invalid' : ''), 'placeholder' => 'Enter company name']) }}
            @error('company_name')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <label class="col-xl-2 col-lg-1 col-form-label">{{ __('Avatar') }}</label>
        <div class="col-lg-4">
            <div class="image-input image-input-outline" id="kt_profile_avatar" style="background-image: url({{ asset(config('constants.IMAGE_PATH.DEFAULT_AVATAR')) }})">
                <div class="image-input-wrapper" style="background-image: url({{ asset($user->avatar_url ?? '') }})"></div>
                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                    <i class="fa fa-pen icon-sm text-muted"></i>
                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                    <input type="hidden" name="avatar_remove" />
                </label>
                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                </span>
                @if(!empty($user->avatar))
                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                </span>
                @endif
            </div>
            <span class="form-text text-muted">{{ __('Allowed file types: png, jpg, jpeg.') }}</span>
            @error('avatar')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
</div>